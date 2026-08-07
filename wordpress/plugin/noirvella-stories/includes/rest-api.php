<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * REST endpoints for creating stories/chapters in bulk (e.g. importing a
 * finished story with all its chapters in one call, instead of one
 * wp_insert_post round-trip per chapter through the block editor).
 * Both endpoints reuse nvl_insert_chapter() so the "parent must be a
 * Story root" rule enforced in cpt.php's save handler has exactly one
 * counterpart here, instead of being duplicated per-route.
 */
function nvl_register_rest_routes() {
    register_rest_route( 'noirvella/v1', '/stories', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'nvl_rest_create_story',
        'permission_callback' => function () {
            return current_user_can( 'publish_posts' );
        },
        'args' => array(
            'title'    => array( 'required' => true, 'type' => 'string' ),
            'content'  => array( 'required' => false, 'type' => 'string', 'default' => '' ),
            'excerpt'  => array( 'required' => false, 'type' => 'string', 'default' => '' ),
            'status'   => array( 'required' => false, 'type' => 'string', 'default' => 'draft' ),
            'chapters' => array( 'required' => false, 'type' => 'array', 'default' => array() ),
        ),
    ) );

    register_rest_route( 'noirvella/v1', '/stories/(?P<id>\d+)/chapters', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'nvl_rest_add_chapter',
        'permission_callback' => function ( $request ) {
            if ( ! current_user_can( 'publish_posts' ) ) return false;

            // Let a missing/wrong-type target fall through to the callback,
            // which returns a proper 404/400 instead of a generic 403 --
            // current_user_can( 'edit_post', $id ) on a nonexistent post ID
            // always evaluates to false, which would otherwise mask the
            // real error.
            $story = get_post( (int) $request['id'] );
            if ( ! $story || $story->post_type !== 'nvl_story' ) return true;

            return current_user_can( 'edit_post', $story->ID );
        },
        'args' => array(
            'title'      => array( 'required' => true, 'type' => 'string' ),
            'content'    => array( 'required' => false, 'type' => 'string', 'default' => '' ),
            'excerpt'    => array( 'required' => false, 'type' => 'string', 'default' => '' ),
            'status'     => array( 'required' => false, 'type' => 'string', 'default' => 'draft' ),
            'menu_order' => array( 'required' => false, 'type' => 'integer', 'default' => 0 ),
        ),
    ) );
}
add_action( 'rest_api_init', 'nvl_register_rest_routes' );

function nvl_rest_sanitize_status( $status ) {
    $allowed = array( 'publish', 'draft', 'pending', 'private', 'future' );
    return in_array( $status, $allowed, true ) ? $status : 'draft';
}

/**
 * POST /wp-json/noirvella/v1/stories
 * Creates a Story root and, optionally, its chapters in one call.
 *
 * Body: { title, content?, excerpt?, status?, chapters?: [ { title, content?, excerpt?, status?, menu_order? }, ... ] }
 */
function nvl_rest_create_story( WP_REST_Request $request ) {
    $title = sanitize_text_field( (string) $request->get_param( 'title' ) );
    if ( '' === trim( $title ) ) {
        return new WP_Error( 'nvl_missing_title', __( 'Story title is required.', 'noirvella-stories' ), array( 'status' => 400 ) );
    }

    $story_id = wp_insert_post( array(
        'post_type'    => 'nvl_story',
        'post_parent'  => 0,
        'post_title'   => $title,
        'post_content' => wp_kses_post( (string) $request->get_param( 'content' ) ),
        'post_excerpt' => sanitize_textarea_field( (string) $request->get_param( 'excerpt' ) ),
        'post_status'  => nvl_rest_sanitize_status( $request->get_param( 'status' ) ),
    ), true );

    if ( is_wp_error( $story_id ) ) {
        return $story_id;
    }

    $created  = array();
    $chapters = $request->get_param( 'chapters' );

    if ( is_array( $chapters ) ) {
        foreach ( array_values( $chapters ) as $i => $chapter ) {
            $result = nvl_insert_chapter( $story_id, is_array( $chapter ) ? $chapter : array(), $i + 1 );
            if ( is_wp_error( $result ) ) {
                return new WP_Error(
                    'nvl_chapter_failed',
                    sprintf(
                        /* translators: 1: story ID, 2: chapter position, 3: underlying error message */
                        __( 'Story created (ID %1$d) but chapter #%2$d failed: %3$s', 'noirvella-stories' ),
                        $story_id,
                        $i + 1,
                        $result->get_error_message()
                    ),
                    array(
                        'status'           => 400,
                        'story_id'         => $story_id,
                        'created_chapters' => $created,
                    )
                );
            }
            $created[] = $result;
        }
    }

    return new WP_REST_Response( array(
        'story_id' => $story_id,
        'title'    => $title,
        'link'     => get_permalink( $story_id ),
        'chapters' => $created,
    ), 201 );
}

/**
 * POST /wp-json/noirvella/v1/stories/{id}/chapters
 * Adds a single chapter under an existing Story root.
 *
 * Body: { title, content?, excerpt?, status?, menu_order? }
 */
function nvl_rest_add_chapter( WP_REST_Request $request ) {
    $story_id = (int) $request['id'];
    $story    = get_post( $story_id );

    if ( ! $story || $story->post_type !== 'nvl_story' ) {
        return new WP_Error( 'nvl_story_not_found', __( 'Story not found.', 'noirvella-stories' ), array( 'status' => 404 ) );
    }
    if ( $story->post_parent ) {
        return new WP_Error( 'nvl_not_a_story', __( 'Target post is a chapter, not a Story — chapters cannot have chapters.', 'noirvella-stories' ), array( 'status' => 400 ) );
    }

    $result = nvl_insert_chapter( $story_id, $request->get_params(), $request->get_param( 'menu_order' ) );
    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return new WP_REST_Response( $result, 201 );
}

/**
 * Shared insert path for both endpoints, so "what makes a valid chapter"
 * is defined once. $default_order is used only when the caller didn't
 * pass menu_order explicitly (e.g. bulk-create numbers chapters 1..N).
 */
function nvl_insert_chapter( $story_id, $data, $default_order = 0 ) {
    $title = isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '';
    if ( '' === trim( $title ) ) {
        return new WP_Error( 'nvl_missing_title', __( 'Chapter title is required.', 'noirvella-stories' ), array( 'status' => 400 ) );
    }

    $chapter_id = wp_insert_post( array(
        'post_type'    => 'nvl_story',
        'post_parent'  => $story_id,
        'post_title'   => $title,
        'post_content' => isset( $data['content'] ) ? wp_kses_post( $data['content'] ) : '',
        'post_excerpt' => isset( $data['excerpt'] ) ? sanitize_textarea_field( $data['excerpt'] ) : '',
        'post_status'  => nvl_rest_sanitize_status( isset( $data['status'] ) ? $data['status'] : 'draft' ),
        'menu_order'   => isset( $data['menu_order'] ) ? absint( $data['menu_order'] ) : absint( $default_order ),
    ), true );

    if ( is_wp_error( $chapter_id ) ) {
        return $chapter_id;
    }

    return array(
        'chapter_id' => $chapter_id,
        'title'      => $title,
        'link'       => get_permalink( $chapter_id ),
        'menu_order' => (int) get_post_field( 'menu_order', $chapter_id ),
    );
}

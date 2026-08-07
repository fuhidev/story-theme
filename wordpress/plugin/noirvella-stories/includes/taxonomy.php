<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Genres. Registered against nvl_story, but conceptually they belong to
 * Story ROOTS only -- a chapter inherits whatever its story is filed
 * under. That rule is enforced in two places below: the genre box is
 * hidden on chapter edit screens, and genre archives are forced to
 * post_parent = 0 so a chapter can never surface as a search result on a
 * genre page.
 */
function nvl_register_taxonomy() {

    $labels = array(
        'name'              => __( 'Genres', 'noirvella-stories' ),
        'singular_name'     => __( 'Genre', 'noirvella-stories' ),
        'search_items'      => __( 'Search genres', 'noirvella-stories' ),
        'all_items'         => __( 'All genres', 'noirvella-stories' ),
        'parent_item'       => __( 'Parent genre', 'noirvella-stories' ),
        'parent_item_colon' => __( 'Parent genre:', 'noirvella-stories' ),
        'edit_item'         => __( 'Edit genre', 'noirvella-stories' ),
        'update_item'       => __( 'Update genre', 'noirvella-stories' ),
        'add_new_item'      => __( 'Add new genre', 'noirvella-stories' ),
        'new_item_name'     => __( 'New genre name', 'noirvella-stories' ),
        'menu_name'         => __( 'Genres', 'noirvella-stories' ),
    );

    register_taxonomy( 'nvl_genre', array( 'nvl_story' ), array(
        'labels'            => $labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array(
            'slug'       => 'genre',
            'with_front' => false,
        ),
    ) );
}
add_action( 'init', 'nvl_register_taxonomy' );

/**
 * A genre archive lists STORIES, not the chapters inside them -- without
 * this, a story with 200 chapters would flood its own genre page with 200
 * near-identical entries. post_parent = 0 is an indexed column, so this
 * costs nothing.
 */
function nvl_genre_archive_stories_only( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! $query->is_tax( 'nvl_genre' ) ) return;

    $query->set( 'post_parent', 0 );
    $query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'nvl_genre_archive_stories_only' );

/**
 * Hide the genre box when editing a chapter. Genres live on the Story
 * root; letting an editor set a different genre per chapter would produce
 * a story whose chapters disagree about what it is.
 */
function nvl_hide_genre_box_on_chapters() {
    global $post;
    if ( $post && $post->post_type === 'nvl_story' && $post->post_parent ) {
        remove_meta_box( 'nvl_genrediv', 'nvl_story', 'side' );
    }
}
add_action( 'add_meta_boxes', 'nvl_hide_genre_box_on_chapters', 20 );

/**
 * Genre terms of a story (resolves a chapter to its story root first, so
 * templates can call this with whatever post they happen to be on).
 */
function nvl_get_story_genres( $post_id ) {
    $root  = wp_get_post_parent_id( $post_id );
    $root  = $root ? $root : $post_id;
    $terms = get_the_terms( $root, 'nvl_genre' );
    return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * All genres that actually have at least one story, cached -- used by the
 * header dropdown and the homepage "browse by genre" chips, both of which
 * render on every page view.
 */
function nvl_get_all_genres() {
    $cached = get_transient( 'nvl_all_genres' );
    if ( false !== $cached ) return $cached;

    $terms = get_terms( array(
        'taxonomy'   => 'nvl_genre',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );

    $terms = ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();

    set_transient( 'nvl_all_genres', $terms, HOUR_IN_SECONDS );
    return $terms;
}

// A term rename/add/delete changes the header nav everywhere, so drop the
// cached list immediately rather than waiting out the hour.
add_action( 'created_nvl_genre', function () { delete_transient( 'nvl_all_genres' ); } );
add_action( 'edited_nvl_genre',  function () { delete_transient( 'nvl_all_genres' ); } );
add_action( 'delete_nvl_genre',  function () { delete_transient( 'nvl_all_genres' ); } );

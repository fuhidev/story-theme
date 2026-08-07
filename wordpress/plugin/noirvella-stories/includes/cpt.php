<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the "Story" post type (parent) and reuse it — chapters are just
 * Story posts with a post_parent set. This means:
 *  - one post type, one set of DB indexes to worry about
 *  - "get chapters of a story" = WP_Query( post_parent => $story_id ) -> uses
 *    the wp_posts.post_parent index, no meta_query, no extra JOIN
 *  - "chapter number" = menu_order, also a native indexed column
 *  - ordering chapters = orderby menu_order, zero extra cost
 */
function nvl_register_post_type() {

    $labels = array(
        'name'          => __( 'Stories', 'noirvella-stories' ),
        'singular_name' => __( 'Story', 'noirvella-stories' ),
        'add_new_item'  => __( 'Add New Story / Chapter', 'noirvella-stories' ),
    );

    register_post_type( 'nvl_story', array(
        'labels'              => $labels,
        'public'              => true,
        'hierarchical'        => true,          // allows post_parent
        'has_archive'         => 'stories',
        'rewrite'             => array(
            'slug'       => 'blog',             // matches /blog/%story%/%chapter%
            'with_front' => false,
        ),
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'           => 'dashicons-book-alt',
        'show_in_rest'        => true,
        'capability_type'     => 'post',
    ) );
}
add_action( 'init', 'nvl_register_post_type' );

/**
 * Custom permalink: /blog/{story-slug}/{chapter-slug}
 * Top-level posts (the "story" root / intro page) keep /blog/{story-slug}
 */
function nvl_custom_permalink( $post_link, $post ) {
    if ( $post->post_type !== 'nvl_story' ) return $post_link;

    if ( $post->post_parent ) {
        $parent = get_post( $post->post_parent );
        if ( $parent ) {
            return home_url( '/blog/' . $parent->post_name . '/' . $post->post_name . '/' );
        }
    }
    return home_url( '/blog/' . $post->post_name . '/' );
}
add_filter( 'post_type_link', 'nvl_custom_permalink', 10, 2 );

/**
 * Replaces the native "Page Attributes" Parent dropdown, which lists every
 * post in the hierarchy (stories AND chapters) and let a chapter be picked
 * as another post's parent. Only Story roots (post_parent = 0) are valid
 * parents, so this box only ever offers those — a chapter can never be
 * selected as a parent, by construction.
 */
function nvl_chapter_meta_box() {
    add_meta_box(
        'nvl_chapter_info',
        __( 'Story / Chapter', 'noirvella-stories' ),
        'nvl_chapter_meta_box_html',
        'nvl_story',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'nvl_chapter_meta_box' );

function nvl_chapter_meta_box_html( $post ) {
    wp_nonce_field( 'nvl_save_relation', 'nvl_relation_nonce' );

    $current_parent = (int) $post->post_parent;

    // Coming from the "Add chapter" row action on a fresh draft — preselect
    // the story it was launched from.
    if ( ! $current_parent && $post->post_status === 'auto-draft' && isset( $_GET['nvl_parent_story'] ) ) {
        $current_parent = absint( $_GET['nvl_parent_story'] );
    }

    $stories = get_posts( array(
        'post_type'      => 'nvl_story',
        'post_parent'    => 0,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
        'exclude'        => array( $post->ID ),
    ) );
    ?>
    <p>
      <label for="nvl_parent_id"><strong><?php esc_html_e( 'Story', 'noirvella-stories' ); ?></strong></label><br>
      <select name="nvl_parent_id" id="nvl_parent_id" style="width:100%;">
        <option value="0" <?php selected( $current_parent, 0 ); ?>><?php esc_html_e( '— This is a Story (no parent) —', 'noirvella-stories' ); ?></option>
        <?php foreach ( $stories as $story ) : ?>
          <option value="<?php echo esc_attr( $story->ID ); ?>" <?php selected( $current_parent, $story->ID ); ?>><?php echo esc_html( $story->post_title ? $story->post_title : '(no title) #' . $story->ID ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="description"><?php esc_html_e( 'Only Stories are listed here — a chapter can never be chosen as a parent.', 'noirvella-stories' ); ?></p>
    </p>
    <p>
      <label for="nvl_menu_order"><strong><?php esc_html_e( 'Chapter order', 'noirvella-stories' ); ?></strong></label><br>
      <input type="number" step="1" min="0" id="nvl_menu_order" name="nvl_menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" style="width:100%;">
      <p class="description"><?php esc_html_e( 'Sort position of this chapter within its Story. Leave 0 for a Story root.', 'noirvella-stories' ); ?></p>
    </p>
    <?php
    /**
     * Extension point for the Story-only fields (author, status, featured)
     * in includes/story-meta.php. They live in this box rather than one of
     * their own so there's a single panel, a single nonce, and a single
     * save path for everything that describes a Story.
     */
    do_action( 'nvl_after_relation_fields', $post );
}

/**
 * Saves the Story/Chapter box. A submitted parent is only honored if it
 * points at an existing Story root — this is what actually enforces "a
 * chapter can't be a parent" server-side (the dropdown alone is just UX).
 */
function nvl_save_relation_meta( $post_id, $post ) {
    if ( ! isset( $_POST['nvl_relation_nonce'] ) || ! wp_verify_nonce( $_POST['nvl_relation_nonce'], 'nvl_save_relation' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $post->post_type !== 'nvl_story' ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Fired before the early-return below: the Story-only fields must save
    // even when the parent/order didn't change (e.g. only the status was
    // edited), which is exactly the case that return short-circuits.
    do_action( 'nvl_save_relation_fields', $post_id, $post );

    $parent_id  = isset( $_POST['nvl_parent_id'] ) ? absint( $_POST['nvl_parent_id'] ) : 0;
    $menu_order = isset( $_POST['nvl_menu_order'] ) ? absint( $_POST['nvl_menu_order'] ) : 0;

    if ( $parent_id ) {
        $parent = get_post( $parent_id );
        $valid  = $parent && $parent->post_type === 'nvl_story' && (int) $parent->post_parent === 0 && $parent_id !== $post_id;
        if ( ! $valid ) {
            $parent_id = 0;
        }
    }

    if ( (int) $post->post_parent === $parent_id && (int) $post->menu_order === $menu_order ) return;

    remove_action( 'save_post_nvl_story', 'nvl_save_relation_meta', 10 );
    wp_update_post( array(
        'ID'          => $post_id,
        'post_parent' => $parent_id,
        'menu_order'  => $menu_order,
    ) );
    add_action( 'save_post_nvl_story', 'nvl_save_relation_meta', 10, 2 );
}
add_action( 'save_post_nvl_story', 'nvl_save_relation_meta', 10, 2 );

/**
 * "Add chapter" row action on Story roots in the admin list table, so
 * creating a chapter starts from the story it belongs to instead of a
 * blank "Add New" screen with no parent context.
 */
function nvl_add_chapter_row_action( $actions, $post ) {
    if ( $post->post_type !== 'nvl_story' || $post->post_parent ) return $actions;
    if ( ! current_user_can( 'edit_posts' ) ) return $actions;

    $url = add_query_arg( array(
        'post_type'        => 'nvl_story',
        'nvl_parent_story' => $post->ID,
    ), admin_url( 'post-new.php' ) );

    $actions['nvl_add_chapter'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Add chapter', 'noirvella-stories' ) . '</a>';
    return $actions;
}
add_filter( 'page_row_actions', 'nvl_add_chapter_row_action', 10, 2 );

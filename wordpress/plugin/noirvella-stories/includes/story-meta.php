<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The fields that describe a Story as a whole: byline, publication status,
 * and whether it takes the homepage hero slot. Rendered inside the
 * existing "Story / Chapter" box (see cpt.php) via the extension hooks, so
 * the editor gets one panel instead of three, and there is one nonce and
 * one save path.
 *
 * All three only apply to Story roots -- a chapter has no separate author
 * or status, it inherits its story's.
 */

const NVL_META_AUTHOR   = '_nvl_author';
const NVL_META_STATUS   = '_nvl_status';
const NVL_META_FEATURED = '_nvl_featured';

function nvl_story_statuses() {
    return array(
        'ongoing'   => __( 'Ongoing', 'noirvella-stories' ),
        'completed' => __( 'Completed', 'noirvella-stories' ),
    );
}

/**
 * Only render for Story roots. On a fresh auto-draft launched from the
 * "Add chapter" row action the parent isn't set on the post object yet, so
 * check the query arg too -- otherwise these fields would flash up on a
 * chapter that's about to have a parent.
 */
function nvl_render_story_fields( $post ) {
    $is_chapter = (bool) $post->post_parent;
    if ( ! $is_chapter && $post->post_status === 'auto-draft' && ! empty( $_GET['nvl_parent_story'] ) ) {
        $is_chapter = true;
    }
    if ( $is_chapter ) return;

    $author   = (string) get_post_meta( $post->ID, NVL_META_AUTHOR, true );
    $status   = (string) get_post_meta( $post->ID, NVL_META_STATUS, true );
    $featured = (bool) get_post_meta( $post->ID, NVL_META_FEATURED, true );
    $statuses = nvl_story_statuses();

    if ( ! isset( $statuses[ $status ] ) ) $status = 'ongoing';
    ?>
    <hr>
    <p>
      <label for="nvl_story_author"><strong><?php esc_html_e( 'Author', 'noirvella-stories' ); ?></strong></label><br>
      <input type="text" id="nvl_story_author" name="nvl_story_author" value="<?php echo esc_attr( $author ); ?>" style="width:100%;">
      <p class="description"><?php esc_html_e( 'Shown as the byline on the story page. Leave blank to hide it.', 'noirvella-stories' ); ?></p>
    </p>
    <p>
      <label for="nvl_story_status"><strong><?php esc_html_e( 'Status', 'noirvella-stories' ); ?></strong></label><br>
      <select name="nvl_story_status" id="nvl_story_status" style="width:100%;">
        <?php foreach ( $statuses as $value => $label ) : ?>
          <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="description"><?php esc_html_e( 'Completed stories also appear in their own section on the homepage.', 'noirvella-stories' ); ?></p>
    </p>
    <p>
      <label for="nvl_story_featured">
        <input type="checkbox" id="nvl_story_featured" name="nvl_story_featured" value="1" <?php checked( $featured ); ?>>
        <strong><?php esc_html_e( 'Feature on homepage', 'noirvella-stories' ); ?></strong>
      </label>
      <p class="description"><?php esc_html_e( 'Takes the large hero slot at the top of the homepage. If several stories are featured, the most recently published one wins.', 'noirvella-stories' ); ?></p>
    </p>
    <?php
}
add_action( 'nvl_after_relation_fields', 'nvl_render_story_fields' );

/**
 * Runs inside nvl_save_relation_meta(), i.e. after its nonce, autosave and
 * capability checks have already passed -- this hook is never reachable
 * without them.
 *
 * The isset() guard on the box's own hidden marker matters: quick-edit and
 * programmatic saves (the REST import endpoints, for one) submit no
 * meta box at all, and without the guard every one of them would wipe
 * these fields back to defaults.
 */
function nvl_save_story_fields( $post_id, $post ) {
    if ( ! isset( $_POST['nvl_parent_id'] ) ) return; // box wasn't on this screen

    $parent_id  = absint( $_POST['nvl_parent_id'] );
    $is_chapter = $parent_id > 0;

    // A post demoted to chapter shouldn't keep story-level data lying around.
    if ( $is_chapter ) {
        delete_post_meta( $post_id, NVL_META_AUTHOR );
        delete_post_meta( $post_id, NVL_META_STATUS );
        delete_post_meta( $post_id, NVL_META_FEATURED );
        return;
    }

    $author = isset( $_POST['nvl_story_author'] ) ? sanitize_text_field( wp_unslash( $_POST['nvl_story_author'] ) ) : '';
    if ( $author !== '' ) {
        update_post_meta( $post_id, NVL_META_AUTHOR, $author );
    } else {
        delete_post_meta( $post_id, NVL_META_AUTHOR );
    }

    $statuses = nvl_story_statuses();
    $status   = isset( $_POST['nvl_story_status'] ) ? sanitize_key( wp_unslash( $_POST['nvl_story_status'] ) ) : 'ongoing';
    if ( ! isset( $statuses[ $status ] ) ) $status = 'ongoing';
    update_post_meta( $post_id, NVL_META_STATUS, $status );

    if ( ! empty( $_POST['nvl_story_featured'] ) ) {
        update_post_meta( $post_id, NVL_META_FEATURED, 1 );
    } else {
        // Deleted rather than set to 0 so the featured lookup can use a
        // plain EXISTS check instead of comparing values.
        delete_post_meta( $post_id, NVL_META_FEATURED );
    }
}
add_action( 'nvl_save_relation_fields', 'nvl_save_story_fields', 10, 2 );

/**
 * Template helpers -- all resolve a chapter to its story root first, so a
 * template can pass whichever post it's holding.
 */
function nvl_get_story_root_id( $post_id ) {
    $parent = wp_get_post_parent_id( $post_id );
    return $parent ? $parent : (int) $post_id;
}

function nvl_get_story_author( $post_id ) {
    return (string) get_post_meta( nvl_get_story_root_id( $post_id ), NVL_META_AUTHOR, true );
}

function nvl_get_story_status( $post_id ) {
    $status   = (string) get_post_meta( nvl_get_story_root_id( $post_id ), NVL_META_STATUS, true );
    $statuses = nvl_story_statuses();
    return isset( $statuses[ $status ] ) ? $status : 'ongoing';
}

function nvl_get_story_status_label( $post_id ) {
    $statuses = nvl_story_statuses();
    return $statuses[ nvl_get_story_status( $post_id ) ];
}

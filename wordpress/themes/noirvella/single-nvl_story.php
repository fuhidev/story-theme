<?php
/**
 * Router for the two very different pages that share this one post type.
 *
 * A Story root and a chapter are both nvl_story posts, so WordPress hands
 * them both to this template -- but a story page is a cover + description
 * + chapter list, and a chapter page is a reader with a sticky chapter
 * picker. The position maths is done once here and passed down, so
 * neither part has to re-walk the chapter list to work out where it is.
 */
get_header();

$post_id    = get_the_ID();
$parent_id  = wp_get_post_parent_id( $post_id );
$is_chapter = (bool) $parent_id;
$story_id   = $is_chapter ? $parent_id : $post_id;
$chapters   = nvl_get_chapters( $story_id ); // ordered by menu_order
$total      = count( $chapters );

$chapter_index = 0; // 1-based position of the current chapter in the list
if ( $is_chapter ) {
    foreach ( $chapters as $i => $chapter ) {
        if ( (int) $chapter->ID === (int) $post_id ) { $chapter_index = $i + 1; break; }
    }
}

$context = array(
    'post_id'       => $post_id,
    'story_id'      => $story_id,
    'chapters'      => $chapters,
    'total'         => $total,
    'chapter_index' => $chapter_index,
    'prev'          => ( $chapter_index > 1 ) ? $chapters[ $chapter_index - 2 ] : null,
    'next'          => ( $chapter_index > 0 && $chapter_index < $total ) ? $chapters[ $chapter_index ] : null,
);

if ( $is_chapter ) {
    get_template_part( 'template-parts/single', 'chapter', $context );
} else {
    get_template_part( 'template-parts/single', 'story', $context );
}

get_footer();

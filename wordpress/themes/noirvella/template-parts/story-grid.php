<?php
/**
 * The story grid, shared by the homepage, the archive, genre pages and
 * search, so the card loop and the in-feed ad placement exist in one
 * place rather than being copy-pasted into four templates.
 *
 * Accepts either a WP_Query ('query') or a plain WP_Post array ('items').
 *
 * $args: items | query, show_views (bool), infeed_after (int|false),
 *        empty_text (string)
 */
$items        = $args['items'] ?? array();
$query        = $args['query'] ?? null;
$show_views   = $args['show_views'] ?? false;
$infeed_after = array_key_exists( 'infeed_after', $args ) ? $args['infeed_after'] : 6;
$empty_text   = $args['empty_text'] ?? '';

$has_items = $query ? $query->have_posts() : ! empty( $items );

if ( ! $has_items ) {
    if ( $empty_text ) echo '<p class="empty-note">' . esc_html( $empty_text ) . '</p>';
    return;
}

// The in-feed unit only makes sense once there are enough cards for it to
// sit *inside* the grid; otherwise it would just be a trailing ad with a
// story card's shape, which is exactly what AdSense's policy on
// distinguishable ads warns against.
$total      = $query ? $query->post_count : count( $items );
$show_infeed = $infeed_after && $total > $infeed_after && function_exists( 'nvl_has_ad_slot' ) && nvl_has_ad_slot( 'infeed' );
$i = 0;
?>
<div class="grid">
  <?php if ( $query ) : ?>
    <?php while ( $query->have_posts() ) : $query->the_post(); $i++; ?>
      <?php get_template_part( 'template-parts/card', 'story', array( 'show_views' => $show_views ) ); ?>
      <?php if ( $show_infeed && $i === $infeed_after ) : ?>
        <div class="grid-ad"><?php nvl_render_ad_slot( 'infeed' ); ?></div>
      <?php endif; ?>
    <?php endwhile; wp_reset_postdata(); ?>
  <?php else : ?>
    <?php foreach ( $items as $item ) : $i++; ?>
      <?php get_template_part( 'template-parts/card', 'story', array( 'post' => $item, 'show_views' => $show_views ) ); ?>
      <?php if ( $show_infeed && $i === $infeed_after ) : ?>
        <div class="grid-ad"><?php nvl_render_ad_slot( 'infeed' ); ?></div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

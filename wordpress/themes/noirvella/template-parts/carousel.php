<?php
/**
 * Horizontal "keep reading" carousel. Pure CSS scroll-snap -- no slider
 * JS/library, so it costs nothing extra on the performance budget and
 * works identically with touch swipe, trackpad, or a mouse wheel.
 *
 * $args: items (WP_Post[]), title, class ('wide' to break out of the
 * reading column on full-width pages)
 */
$title = $args['title'] ?? __( 'You may also like', 'noirvella' );
$items = $args['items'] ?? array();
$class = $args['class'] ?? '';
if ( empty( $items ) ) return;
?>
<div class="story-carousel <?php echo esc_attr( $class ); ?>">
  <?php if ( $title !== '' ) : ?>
    <span class="eyebrow"><?php echo esc_html( $title ); ?></span>
  <?php endif; ?>
  <div class="carousel-track">
    <?php foreach ( $items as $item ) : ?>
      <a class="carousel-card" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
        <div class="carousel-cover">
          <?php if ( has_post_thumbnail( $item ) ) : ?>
            <?php echo get_the_post_thumbnail( $item, 'nvl-poster', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
          <?php endif; ?>
          <span class="carousel-title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

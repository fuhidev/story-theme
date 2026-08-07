<?php
/**
 * Heading used by every homepage section, so they line up with each other
 * instead of each block inventing its own title treatment.
 *
 * $args: title (required), eyebrow, link, link_text
 */
$title     = $args['title'] ?? '';
$eyebrow   = $args['eyebrow'] ?? '';
$link      = $args['link'] ?? '';
$link_text = $args['link_text'] ?? __( 'See all', 'noirvella' );
if ( $title === '' ) return;
?>
<div class="section-head">
  <div>
    <?php if ( $eyebrow ) : ?>
      <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
    <?php endif; ?>
    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
  </div>
  <?php if ( $link ) : ?>
    <a class="section-link" href="<?php echo esc_url( $link ); ?>">
      <span><?php echo esc_html( $link_text ); ?></span><span aria-hidden="true">&rarr;</span>
    </a>
  <?php endif; ?>
</div>

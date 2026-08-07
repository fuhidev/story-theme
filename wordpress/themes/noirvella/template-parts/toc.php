<?php
/**
 * Table of contents -- the chapter picker on the reader page.
 *
 * Rendered exactly once, with two presentations decided entirely in CSS:
 *  - >= 1024px: a sticky panel in the right-hand column, so a reader can
 *    jump chapters from anywhere in the text without scrolling to the end.
 *  - < 1024px: the floating round button it has always been, expanding
 *    into a scrollable panel (see the .toc rules in main.css and the
 *    open/close handling in main.js).
 *
 * The ids and class names below are load-bearing for main.js -- #nvl-toc,
 * .toc-head and .toc-list in particular.
 *
 * $args: chapters (WP_Post[]), current (int), story_id (int), prev, next
 */
$chapters = $args['chapters'] ?? array();
$current  = $args['current'] ?? 0;
$story_id = $args['story_id'] ?? 0;
$prev     = $args['prev'] ?? null;
$next     = $args['next'] ?? null;
$total    = count( $chapters );
if ( ! $total ) return;
?>
<div class="toc" id="nvl-toc">
  <button class="toc-head" type="button" aria-expanded="false" aria-controls="nvl-toc-list">
    <span class="toc-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 5H16M4 10H16M4 15H12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
      </svg>
    </span>
    <span class="toc-label">
      <span class="eyebrow"><?php esc_html_e( 'Table of contents', 'noirvella' ); ?></span>
      <span class="toc-count"><?php echo esc_html( sprintf( _n( '%s chapter', '%s chapters', $total, 'noirvella' ), number_format_i18n( $total ) ) ); ?></span>
    </span>
  </button>

  <?php if ( $story_id ) : ?>
    <a class="toc-story" href="<?php echo esc_url( get_permalink( $story_id ) ); ?>">
      <?php if ( has_post_thumbnail( $story_id ) ) : ?>
        <span class="toc-story-thumb"><?php echo get_the_post_thumbnail( $story_id, 'nvl-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?></span>
      <?php endif; ?>
      <span class="toc-story-title"><?php echo esc_html( get_the_title( $story_id ) ); ?></span>
    </a>
  <?php endif; ?>

  <ul class="toc-list" id="nvl-toc-list">
    <?php foreach ( $chapters as $i => $chapter ) : ?>
      <li class="<?php echo ( (int) $chapter->ID === (int) $current ) ? 'current' : ''; ?>">
        <span class="tnum"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
        <a href="<?php echo esc_url( get_permalink( $chapter ) ); ?>"><?php echo esc_html( $chapter->post_title ); ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ( $prev || $next ) : ?>
    <div class="toc-steps">
      <?php if ( $prev ) : ?>
        <a class="toc-step" href="<?php echo esc_url( get_permalink( $prev ) ); ?>" aria-label="<?php esc_attr_e( 'Previous chapter', 'noirvella' ); ?>">&larr;</a>
      <?php else : ?>
        <span class="toc-step is-disabled" aria-hidden="true">&larr;</span>
      <?php endif; ?>
      <?php if ( $next ) : ?>
        <a class="toc-step" href="<?php echo esc_url( get_permalink( $next ) ); ?>" aria-label="<?php esc_attr_e( 'Next chapter', 'noirvella' ); ?>">&rarr;</a>
      <?php else : ?>
        <span class="toc-step is-disabled" aria-hidden="true">&rarr;</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

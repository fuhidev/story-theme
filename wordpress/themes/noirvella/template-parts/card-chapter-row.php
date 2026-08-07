<?php
/**
 * One row of the homepage "Latest updates" list: the story it belongs to
 * on the left, the chapter that just went up on the right. This is the
 * section a returning reader scans first, so both links are separate --
 * the story title goes to the story page, the chapter title straight into
 * the text.
 *
 * $args: chapter (WP_Post, required)
 */
$chapter = $args['chapter'] ?? null;
if ( ! $chapter ) return;

$story_id = $chapter->post_parent;
if ( ! $story_id ) return;

$chapters = nvl_get_chapters( $story_id );
$number   = 0;
foreach ( $chapters as $i => $item ) {
    if ( (int) $item->ID === (int) $chapter->ID ) { $number = $i + 1; break; }
}
?>
<li class="update-row">
  <a class="update-thumb" href="<?php echo esc_url( get_permalink( $story_id ) ); ?>" tabindex="-1" aria-hidden="true">
    <?php if ( has_post_thumbnail( $story_id ) ) : ?>
      <?php echo get_the_post_thumbnail( $story_id, 'nvl-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
    <?php endif; ?>
  </a>
  <div class="update-body">
    <a class="update-story" href="<?php echo esc_url( get_permalink( $story_id ) ); ?>"><?php echo esc_html( get_the_title( $story_id ) ); ?></a>
    <a class="update-chapter" href="<?php echo esc_url( get_permalink( $chapter ) ); ?>">
      <?php if ( $number ) : ?>
        <span class="update-num"><?php echo esc_html( sprintf( __( 'Ch. %d', 'noirvella' ), $number ) ); ?></span>
      <?php endif; ?>
      <span><?php echo esc_html( $chapter->post_title ); ?></span>
    </a>
  </div>
  <time class="update-time" datetime="<?php echo esc_attr( get_the_date( 'c', $chapter ) ); ?>">
    <?php echo esc_html( sprintf( __( '%s ago', 'noirvella' ), human_time_diff( get_post_time( 'U', true, $chapter ), time() ) ) ); ?>
  </time>
</li>

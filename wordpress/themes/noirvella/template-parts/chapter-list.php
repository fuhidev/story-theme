<?php
/**
 * The full chapter list on a story page.
 *
 * Long stories get a scrollable panel rather than server-side pagination:
 * paging a chapter list means a reader hunting for chapter 140 has to
 * guess which page it's on, whereas one scrollable list plus the filter
 * box finds it immediately -- and the filter can only work over rows that
 * are actually in the DOM.
 *
 * $args: chapters (WP_Post[]), current (int)
 */
$chapters = $args['chapters'] ?? array();
$current  = $args['current'] ?? 0;
$total    = count( $chapters );
if ( ! $total ) {
    echo '<p class="empty-note">' . esc_html__( 'No chapters published yet.', 'noirvella' ) . '</p>';
    return;
}
?>
<div class="chapter-index<?php echo $total > 60 ? ' is-long' : ''; ?>" id="nvl-chapter-index">
  <div class="chapter-index-bar">
    <label class="chapter-search">
      <span class="screen-reader-text"><?php esc_html_e( 'Filter chapters', 'noirvella' ); ?></span>
      <input type="search" id="nvl-chapter-filter" placeholder="<?php esc_attr_e( 'Filter chapters…', 'noirvella' ); ?>" autocomplete="off">
    </label>
    <button type="button" class="chapter-order" id="nvl-chapter-order" data-order="asc">
      <span aria-hidden="true">&darr;&uarr;</span>
      <span class="chapter-order-label"><?php esc_html_e( 'Oldest first', 'noirvella' ); ?></span>
    </button>
  </div>

  <ol class="chapter-index-list" id="nvl-chapter-index-list">
    <?php foreach ( $chapters as $i => $chapter ) : ?>
      <li class="chapter-index-item<?php echo ( (int) $chapter->ID === (int) $current ) ? ' current' : ''; ?>" data-title="<?php echo esc_attr( strtolower( $chapter->post_title ) ); ?>" data-num="<?php echo esc_attr( $i + 1 ); ?>">
        <a href="<?php echo esc_url( get_permalink( $chapter ) ); ?>">
          <span class="tnum"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
          <span class="chapter-index-title"><?php echo esc_html( $chapter->post_title ); ?></span>
          <time class="chapter-index-date" datetime="<?php echo esc_attr( get_the_date( 'c', $chapter ) ); ?>"><?php echo esc_html( get_the_date( 'M d, Y', $chapter ) ); ?></time>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>

  <p class="chapter-index-empty" hidden><?php esc_html_e( 'No chapter matches that search.', 'noirvella' ); ?></p>
</div>

<?php
/**
 * Story card.
 *
 * Works two ways so the same markup serves every listing on the site:
 *  - inside a WP_Query loop (homepage grid, archive, search) -- called with
 *    no args, uses whatever the_post() set up;
 *  - over a plain WP_Post array from the nvl_get_* helpers -- called with
 *    $args['post'], and sets the loop up itself.
 *
 * $args: post, show_views (bool)
 */
$card_post   = $args['post'] ?? null;
$show_views  = $args['show_views'] ?? false;
$owns_loop   = false;

if ( $card_post ) {
    global $post;
    $post = $card_post;
    setup_postdata( $post );
    $owns_loop = true;
}

$story_id = get_the_ID();
$genres   = function_exists( 'nvl_get_story_genres' ) ? nvl_get_story_genres( $story_id ) : array();
$status   = function_exists( 'nvl_get_story_status' ) ? nvl_get_story_status( $story_id ) : '';
$chapters = count( nvl_get_chapters( $story_id ) );
$views    = function_exists( 'nvl_get_views' ) ? nvl_get_views( $story_id ) : 0;
?>
<div class="card">
  <a class="cover" href="<?php the_permalink(); ?>">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'nvl-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
    <?php endif; ?>
    <?php if ( $status === 'completed' ) : ?>
      <span class="pill pill-done"><?php esc_html_e( 'Completed', 'noirvella' ); ?></span>
    <?php endif; ?>
    <span class="cover-title"><?php the_title(); ?></span>
  </a>
  <div class="card-body">
    <?php if ( $genres ) : ?>
      <div class="badge-row">
        <?php foreach ( array_slice( $genres, 0, 2 ) as $genre ) :
          $genre_url = get_term_link( $genre );
          if ( is_wp_error( $genre_url ) ) continue; ?>
          <a class="badge" href="<?php echo esc_url( $genre_url ); ?>"><?php echo esc_html( $genre->name ); ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="meta-row">
      <span><?php echo esc_html( sprintf( _n( '%s chapter', '%s chapters', $chapters, 'noirvella' ), number_format_i18n( $chapters ) ) ); ?></span>
      <?php if ( $show_views && $views > 0 ) : ?>
        <span><?php echo esc_html( sprintf( __( '%s reads', 'noirvella' ), nvl_format_views( $views ) ) ); ?></span>
      <?php else : ?>
        <span><?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></span>
      <?php endif; ?>
    </div>

    <p class="card-excerpt"><?php the_excerpt(); ?></p>

    <a class="read-link" href="<?php the_permalink(); ?>">
      <span><?php esc_html_e( 'Read story', 'noirvella' ); ?></span><span aria-hidden="true">&rarr;</span>
    </a>
  </div>
</div>
<?php if ( $owns_loop ) wp_reset_postdata(); ?>

<?php
/**
 * The complete, paginated story library at /stories. This is where the
 * homepage's "See all" links land -- the homepage itself is a curated set
 * of sections, so browsing everything happens here.
 *
 * Uses nvl_get_recent_stories() rather than the main query so the listing
 * is served from the same transient the homepage warms, and so chapters
 * can't leak into a list that is meant to be stories only.
 */
get_header();

$paged   = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
$stories = nvl_get_recent_stories( $paged, 12 );
$genres  = nvl_get_all_genres();
?>

<main class="home-wrap">

  <div class="page-head">
    <span class="eyebrow"><?php esc_html_e( 'The library', 'noirvella' ); ?></span>
    <h1><?php esc_html_e( 'All stories', 'noirvella' ); ?></h1>
    <p><?php esc_html_e( 'Every serial on the site, newest first.', 'noirvella' ); ?></p>
  </div>

  <?php if ( $genres ) : ?>
    <div class="genre-chips">
      <?php foreach ( $genres as $genre ) :
        $genre_url = get_term_link( $genre );
        if ( is_wp_error( $genre_url ) ) continue; ?>
        <a class="chip" href="<?php echo esc_url( $genre_url ); ?>">
          <?php echo esc_html( $genre->name ); ?>
          <span class="chip-count"><?php echo esc_html( number_format_i18n( $genre->count ) ); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php nvl_render_ad_slot( 'archive' ); ?>

  <?php get_template_part( 'template-parts/story-grid', null, array(
      'query'      => $stories,
      'empty_text' => __( 'No stories published yet.', 'noirvella' ),
  ) ); ?>

  <?php if ( $stories->max_num_pages > 1 ) : ?>
    <div class="pager">
      <?php
      echo paginate_links( array(
          'total'     => $stories->max_num_pages,
          'current'   => $paged,
          'prev_text' => '&larr; ' . __( 'Previous', 'noirvella' ),
          'next_text' => __( 'Next', 'noirvella' ) . ' &rarr;',
      ) );
      ?>
    </div>
  <?php endif; ?>

</main>

<?php get_footer(); ?>

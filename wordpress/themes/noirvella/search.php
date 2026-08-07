<?php
/**
 * Search results. The main query is narrowed to story roots in
 * functions.php (nvl_restrict_search), so everything here is a story and
 * the same grid the rest of the site uses applies unchanged.
 */
get_header();

$term  = get_search_query();
$found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main class="home-wrap">

  <div class="page-head">
    <span class="eyebrow"><?php esc_html_e( 'Search', 'noirvella' ); ?></span>
    <h1><?php echo esc_html( sprintf( __( 'Results for “%s”', 'noirvella' ), $term ) ); ?></h1>
    <p><?php echo esc_html( sprintf( _n( '%s story found.', '%s stories found.', $found, 'noirvella' ), number_format_i18n( $found ) ) ); ?></p>
    <?php get_search_form(); ?>
  </div>

  <?php nvl_render_ad_slot( 'archive' ); ?>

  <?php get_template_part( 'template-parts/story-grid', null, array(
      'query'      => $GLOBALS['wp_query'],
      'empty_text' => __( 'Nothing matched that search. Try a shorter phrase, or browse by genre.', 'noirvella' ),
  ) ); ?>

  <?php if ( $GLOBALS['wp_query']->max_num_pages > 1 ) : ?>
    <div class="pager">
      <?php
      echo paginate_links( array(
          'prev_text' => '&larr; ' . __( 'Previous', 'noirvella' ),
          'next_text' => __( 'Next', 'noirvella' ) . ' &rarr;',
      ) );
      ?>
    </div>
  <?php endif; ?>

</main>

<?php get_footer(); ?>

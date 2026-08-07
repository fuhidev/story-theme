<?php
/**
 * Genre archive. Unlike the other listings this one runs off the main
 * query -- pre_get_posts in the plugin's taxonomy.php already constrains
 * it to story roots and sets the page size, so re-querying here would
 * duplicate work and break pagination.
 */
get_header();

$term = get_queried_object();
?>

<main class="home-wrap">

  <div class="page-head">
    <span class="eyebrow"><?php esc_html_e( 'Genre', 'noirvella' ); ?></span>
    <h1><?php echo esc_html( $term ? $term->name : __( 'Genre', 'noirvella' ) ); ?></h1>
    <?php if ( $term && $term->description ) : ?>
      <p><?php echo esc_html( $term->description ); ?></p>
    <?php else : ?>
      <p><?php echo esc_html( sprintf( _n( '%s story in this genre.', '%s stories in this genre.', $term ? $term->count : 0, 'noirvella' ), number_format_i18n( $term ? $term->count : 0 ) ) ); ?></p>
    <?php endif; ?>
  </div>

  <?php nvl_render_ad_slot( 'archive' ); ?>

  <?php get_template_part( 'template-parts/story-grid', null, array(
      'query'      => $GLOBALS['wp_query'],
      'empty_text' => __( 'No stories filed under this genre yet.', 'noirvella' ),
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

<?php
/**
 * Fallback template required by WordPress for every theme to be considered
 * valid. front-page.php handles the homepage, single-nvl_story.php handles
 * story/chapter pages, and there are dedicated templates for the archive,
 * genre and search views -- so this file only ever runs for URL types the
 * theme has nothing specific for (a standalone Page, a 404, a date
 * archive). Kept intentionally simple.
 */
get_header();
?>
<main class="page-wrap">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article class="prose">
      <h1 class="page-title"><?php the_title(); ?></h1>
      <?php the_content(); ?>
    </article>
  <?php endwhile; else : ?>
    <div class="page-head">
      <span class="eyebrow"><?php esc_html_e( 'Nothing here', 'noirvella' ); ?></span>
      <h1><?php esc_html_e( 'Page not found', 'noirvella' ); ?></h1>
      <p><?php esc_html_e( 'That link has gone missing. Try the library instead.', 'noirvella' ); ?></p>
      <p><a class="btn btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'nvl_story' ) ); ?>"><?php esc_html_e( 'Browse all stories', 'noirvella' ); ?></a></p>
    </div>
  <?php endif; ?>
</main>
<?php get_footer(); ?>

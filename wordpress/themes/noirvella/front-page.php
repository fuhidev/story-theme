<?php
/**
 * Homepage. A landing page made of sections rather than one flat feed --
 * the complete paginated list of stories lives at the post type archive
 * (/stories), which every "See all" link below points to.
 *
 * Each section renders only when it has something to show, so a brand-new
 * site with three stories doesn't display four empty headings.
 */
get_header();

/**
 * Every section below is built from plugin helpers. If the plugin is
 * deactivated the homepage is the one template still reachable (the story
 * URLs 404 without the post type), so it degrades to a notice instead of
 * fataling on the first undefined function.
 */
if ( ! function_exists( 'nvl_get_featured_story' ) ) {
    echo '<main class="home-wrap"><div class="page-head"><h1>' . esc_html__( 'Setup incomplete', 'noirvella' ) . '</h1><p>' . esc_html__( 'The Noirvella Stories plugin needs to be activated before this theme can show anything.', 'noirvella' ) . '</p></div></main>';
    get_footer();
    return;
}

$archive_url = get_post_type_archive_link( 'nvl_story' );
$featured    = nvl_get_featured_story();
$updates     = nvl_get_latest_chapters( 8 );
$popular     = nvl_get_popular_stories( 6 );
$recent      = nvl_get_recent_stories( 1, 12 );
$completed   = nvl_get_completed_stories( 8 );
$genres      = nvl_get_all_genres();
?>

<main class="home-wrap">

  <?php if ( $featured ) :
    $featured_chapters = nvl_get_chapters( $featured->ID );
    $first_chapter     = $featured_chapters ? $featured_chapters[0] : null;
    $featured_genres   = nvl_get_story_genres( $featured->ID );
    ?>
    <section class="hero-feature">
      <div class="hero-feature-cover">
        <?php if ( has_post_thumbnail( $featured->ID ) ) : ?>
          <?php echo get_the_post_thumbnail( $featured->ID, 'nvl-chapter-cover', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
        <?php endif; ?>
      </div>
      <div class="hero-feature-body">
        <span class="eyebrow"><?php esc_html_e( 'Featured story', 'noirvella' ); ?></span>
        <h1 class="hero-feature-title"><a href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php echo esc_html( get_the_title( $featured ) ); ?></a></h1>

        <div class="badge-row">
          <?php foreach ( $featured_genres as $genre ) :
            $genre_url = get_term_link( $genre );
            if ( is_wp_error( $genre_url ) ) continue; ?>
            <a class="badge" href="<?php echo esc_url( $genre_url ); ?>"><?php echo esc_html( $genre->name ); ?></a>
          <?php endforeach; ?>
          <span class="pill pill-<?php echo esc_attr( nvl_get_story_status( $featured->ID ) ); ?>"><?php echo esc_html( nvl_get_story_status_label( $featured->ID ) ); ?></span>
        </div>

        <p class="hero-feature-excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $featured ) ) ); ?></p>

        <div class="hero-feature-meta">
          <span><?php echo esc_html( sprintf( _n( '%s chapter', '%s chapters', count( $featured_chapters ), 'noirvella' ), number_format_i18n( count( $featured_chapters ) ) ) ); ?></span>
          <?php $featured_views = nvl_get_views( $featured->ID ); ?>
          <?php if ( $featured_views > 0 ) : ?>
            <span><?php echo esc_html( sprintf( __( '%s reads', 'noirvella' ), nvl_format_views( $featured_views ) ) ); ?></span>
          <?php endif; ?>
        </div>

        <div class="hero-feature-actions">
          <?php if ( $first_chapter ) : ?>
            <a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $first_chapter ) ); ?>"><?php esc_html_e( 'Start reading', 'noirvella' ); ?></a>
          <?php endif; ?>
          <a class="btn" href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php esc_html_e( 'Story details', 'noirvella' ); ?></a>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php nvl_render_ad_slot( 'home_top' ); ?>

  <?php
  /**
   * Continue reading. Rendered empty and unhidden by main.js from
   * localStorage -- reading history is per-browser, so it can't come from
   * PHP without breaking every layer of page caching in front of this
   * site.
   */
  ?>
  <section class="home-section" id="nvl-continue" hidden>
    <?php get_template_part( 'template-parts/section-header', null, array(
        'eyebrow' => __( 'Pick up where you left off', 'noirvella' ),
        'title'   => __( 'Continue reading', 'noirvella' ),
    ) ); ?>
    <div class="continue-track" id="nvl-continue-track"></div>
  </section>

  <?php if ( $updates ) : ?>
    <section class="home-section">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'Fresh chapters', 'noirvella' ),
          'title'   => __( 'Latest updates', 'noirvella' ),
          'link'    => $archive_url,
      ) ); ?>
      <ul class="update-list">
        <?php foreach ( $updates as $chapter ) : ?>
          <?php get_template_part( 'template-parts/card-chapter-row', null, array( 'chapter' => $chapter ) ); ?>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

  <?php nvl_render_ad_slot( 'home_mid' ); ?>

  <?php if ( $popular ) : ?>
    <section class="home-section">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'Most read', 'noirvella' ),
          'title'   => __( 'Reader favourites', 'noirvella' ),
          'link'    => $archive_url,
      ) ); ?>
      <ol class="rank-list">
        <?php foreach ( $popular as $rank => $story ) :
          $story_views = nvl_get_views( $story->ID ); ?>
          <li class="rank-item">
            <span class="rank-num"><?php echo esc_html( str_pad( $rank + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
            <a class="rank-thumb" href="<?php echo esc_url( get_permalink( $story ) ); ?>" tabindex="-1" aria-hidden="true">
              <?php if ( has_post_thumbnail( $story->ID ) ) : ?>
                <?php echo get_the_post_thumbnail( $story->ID, 'nvl-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
              <?php endif; ?>
            </a>
            <div class="rank-body">
              <a class="rank-title" href="<?php echo esc_url( get_permalink( $story ) ); ?>"><?php echo esc_html( get_the_title( $story ) ); ?></a>
              <span class="rank-meta">
                <?php echo esc_html( sprintf( _n( '%s chapter', '%s chapters', count( nvl_get_chapters( $story->ID ) ), 'noirvella' ), number_format_i18n( count( nvl_get_chapters( $story->ID ) ) ) ) ); ?>
                <?php if ( $story_views > 0 ) : ?>
                  &middot; <?php echo esc_html( sprintf( __( '%s reads', 'noirvella' ), nvl_format_views( $story_views ) ) ); ?>
                <?php endif; ?>
              </span>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>
  <?php endif; ?>

  <?php if ( $recent->have_posts() ) : ?>
    <section class="home-section">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'Just published', 'noirvella' ),
          'title'   => __( 'New stories', 'noirvella' ),
          'link'    => $archive_url,
      ) ); ?>
      <?php get_template_part( 'template-parts/story-grid', null, array( 'query' => $recent ) ); ?>
    </section>
  <?php endif; ?>

  <?php if ( $completed ) : ?>
    <section class="home-section">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'Read in one sitting', 'noirvella' ),
          'title'   => __( 'Completed stories', 'noirvella' ),
      ) ); ?>
      <?php get_template_part( 'template-parts/carousel', null, array(
          'title' => '',
          'class' => 'wide',
          'items' => $completed,
      ) ); ?>
    </section>
  <?php endif; ?>

  <?php if ( $genres ) : ?>
    <section class="home-section">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'Find your next read', 'noirvella' ),
          'title'   => __( 'Browse by genre', 'noirvella' ),
      ) ); ?>
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
    </section>
  <?php endif; ?>

  <?php nvl_render_ad_slot( 'home_bottom' ); ?>

</main>

<?php get_footer(); ?>

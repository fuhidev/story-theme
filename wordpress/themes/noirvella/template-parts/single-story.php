<?php
/**
 * The story detail page: cover, description, everything that describes the
 * work, and the full chapter list. This is the page a reader lands on from
 * search or a share link, so both entry points into the text -- chapter one
 * and the newest chapter -- are above the fold.
 *
 * $args comes from single-nvl_story.php.
 */
$story_id = $args['story_id'] ?? get_the_ID();
$chapters = $args['chapters'] ?? array();
$total    = $args['total'] ?? count( $chapters );

$first_chapter  = $chapters ? $chapters[0] : null;
$latest_chapter = nvl_get_latest_chapter( $story_id );
$genres         = nvl_get_story_genres( $story_id );
$author         = nvl_get_story_author( $story_id );
$status         = nvl_get_story_status( $story_id );
$views          = nvl_get_views( $story_id );
$description    = trim( get_the_content() );
?>

<main class="story-wrap">

  <?php get_template_part( 'template-parts/breadcrumb', null, array( 'story_id' => $story_id ) ); ?>

  <article class="story-detail">

    <header class="story-header">
      <div class="story-poster">
        <?php if ( has_post_thumbnail( $story_id ) ) : ?>
          <?php echo get_the_post_thumbnail( $story_id, 'nvl-poster', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
        <?php endif; ?>
      </div>

      <div class="story-summary">
        <h1 class="story-title"><?php the_title(); ?></h1>

        <?php if ( $author ) : ?>
          <p class="story-author"><?php echo esc_html( sprintf( __( 'by %s', 'noirvella' ), $author ) ); ?></p>
        <?php endif; ?>

        <?php if ( $genres ) : ?>
          <div class="badge-row">
            <?php foreach ( $genres as $genre ) :
              $genre_url = get_term_link( $genre );
              if ( is_wp_error( $genre_url ) ) continue; ?>
              <a class="badge" href="<?php echo esc_url( $genre_url ); ?>"><?php echo esc_html( $genre->name ); ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <dl class="story-facts">
          <div>
            <dt><?php esc_html_e( 'Status', 'noirvella' ); ?></dt>
            <dd><span class="pill pill-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( nvl_get_story_status_label( $story_id ) ); ?></span></dd>
          </div>
          <div>
            <dt><?php esc_html_e( 'Chapters', 'noirvella' ); ?></dt>
            <dd><?php echo esc_html( number_format_i18n( $total ) ); ?></dd>
          </div>
          <?php if ( $views > 0 ) : ?>
            <div>
              <dt><?php esc_html_e( 'Reads', 'noirvella' ); ?></dt>
              <dd><?php echo esc_html( nvl_format_views( $views ) ); ?></dd>
            </div>
          <?php endif; ?>
          <?php if ( $latest_chapter ) : ?>
            <div>
              <dt><?php esc_html_e( 'Updated', 'noirvella' ); ?></dt>
              <dd><?php echo esc_html( get_the_date( 'M d, Y', $latest_chapter ) ); ?></dd>
            </div>
          <?php endif; ?>
        </dl>

        <div class="story-actions">
          <?php if ( $first_chapter ) : ?>
            <a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $first_chapter ) ); ?>"><?php esc_html_e( 'Read chapter 1', 'noirvella' ); ?></a>
          <?php endif; ?>
          <?php if ( $latest_chapter && $first_chapter && (int) $latest_chapter->ID !== (int) $first_chapter->ID ) : ?>
            <a class="btn" href="<?php echo esc_url( get_permalink( $latest_chapter ) ); ?>"><?php esc_html_e( 'Read latest', 'noirvella' ); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <?php if ( $description !== '' ) : ?>
      <section class="story-description" id="nvl-story-description">
        <h2 class="section-title-sm"><?php esc_html_e( 'About this story', 'noirvella' ); ?></h2>
        <div class="prose is-clamped" id="nvl-story-description-body"><?php the_content(); ?></div>
        <button type="button" class="show-more" id="nvl-story-description-toggle" aria-expanded="false" aria-controls="nvl-story-description-body" hidden>
          <?php esc_html_e( 'Show more', 'noirvella' ); ?>
        </button>
      </section>
    <?php endif; ?>

    <?php nvl_render_ad_slot( 'story_top' ); ?>

    <section class="story-chapters">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => sprintf( _n( '%s chapter', '%s chapters', $total, 'noirvella' ), number_format_i18n( $total ) ),
          'title'   => __( 'Chapters', 'noirvella' ),
      ) ); ?>
      <?php get_template_part( 'template-parts/chapter-list', null, array(
          'chapters' => $chapters,
      ) ); ?>
    </section>

    <?php nvl_render_ad_slot( 'story_bottom' ); ?>

    <section class="story-related">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'More like this', 'noirvella' ),
          'title'   => __( 'You may also like', 'noirvella' ),
      ) ); ?>
      <?php get_template_part( 'template-parts/story-grid', null, array(
          'items'        => nvl_get_related_stories( $story_id, 6 ),
          'infeed_after' => false,
      ) ); ?>
    </section>

  </article>
</main>

<?php
/**
 * The chapter reader.
 *
 * Two columns from 1024px up: the text on the left, and a sticky aside on
 * the right holding the table of contents and the sidebar ad. Below that
 * breakpoint the aside's TOC turns into the floating button it has always
 * been (all in CSS -- the markup is identical either way, which is why
 * there is exactly one #nvl-toc on the page and main.js needs no branch).
 *
 * Nothing in the wrappers below may take a transform, filter or contain
 * property: the mobile TOC is position:fixed, and any of those on an
 * ancestor would re-root it to that element instead of the viewport.
 *
 * $args comes from single-nvl_story.php.
 */
$post_id       = $args['post_id'] ?? get_the_ID();
$story_id      = $args['story_id'] ?? 0;
$chapters      = $args['chapters'] ?? array();
$total         = $args['total'] ?? count( $chapters );
$chapter_index = $args['chapter_index'] ?? 0;
$prev          = $args['prev'] ?? null;
$next          = $args['next'] ?? null;

$related = nvl_get_related_stories( $story_id, 6 );
?>

<main class="chapter-wrap"
      data-story-id="<?php echo esc_attr( $story_id ); ?>"
      data-story-title="<?php echo esc_attr( get_the_title( $story_id ) ); ?>"
      data-chapter-title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
      data-chapter-number="<?php echo esc_attr( $chapter_index ); ?>">

  <?php get_template_part( 'template-parts/breadcrumb', null, array(
      'story_id' => $story_id,
      'chapter'  => get_post( $post_id ),
  ) ); ?>

  <div class="chapter-layout">

    <div class="chapter-main">

      <div class="chapter-head">
        <?php if ( $chapter_index ) : ?>
          <div class="chapter-num"><?php echo esc_html( str_pad( $chapter_index, 2, '0', STR_PAD_LEFT ) ); ?></div>
        <?php endif; ?>
        <h1 class="chapter-title"><?php the_title(); ?></h1>
      </div>

      <?php if ( $total > 0 && $chapter_index ) : ?>
        <div class="progress-track">
          <div class="progress-fill" style="width:<?php echo esc_attr( round( $chapter_index / $total * 100, 1 ) ); ?>%;"></div>
        </div>
        <div class="progress-label">
          <span><?php echo esc_html( get_the_title( $story_id ) ); ?></span>
          <span><?php echo esc_html( sprintf( __( '%1$d of %2$d', 'noirvella' ), $chapter_index, $total ) ); ?></span>
        </div>
      <?php endif; ?>

      <?php if ( has_post_thumbnail() ) : ?>
        <div class="chapter-cover">
          <?php the_post_thumbnail( 'nvl-chapter-cover', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
        </div>
      <?php endif; ?>

      <?php nvl_render_ad_slot( 'top' ); ?>

      <?php
      $rendered_content = apply_filters( 'the_content', get_the_content() );
      list( $content_part1, $content_part2 ) = nvl_split_content_at_percent( $rendered_content, 0.3 );
      ?>

      <div class="prose"><?php echo $content_part1; ?></div>

      <?php if ( $content_part2 !== '' ) : ?>
        <?php nvl_render_ad_slot( 'mid' ); ?>
        <div class="prose"><?php echo $content_part2; ?></div>
      <?php endif; ?>

      <div class="end-marker"><?php echo esc_html( sprintf( __( 'End of chapter %d', 'noirvella' ), $chapter_index ) ); ?></div>

      <div class="chapter-nav">
        <?php if ( $prev ) : ?>
          <a class="nav-btn" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
            <span class="nav-btn-dir">&larr; <?php esc_html_e( 'Previous', 'noirvella' ); ?></span>
            <span class="nav-btn-title"><?php echo esc_html( $prev->post_title ); ?></span>
          </a>
        <?php else : ?>
          <a class="nav-btn" href="<?php echo esc_url( get_permalink( $story_id ) ); ?>">
            <span class="nav-btn-dir">&larr; <?php esc_html_e( 'Story', 'noirvella' ); ?></span>
            <span class="nav-btn-title"><?php esc_html_e( 'Back to details', 'noirvella' ); ?></span>
          </a>
        <?php endif; ?>

        <?php if ( $next ) : ?>
          <a class="nav-btn next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
            <span class="nav-btn-dir"><?php esc_html_e( 'Next', 'noirvella' ); ?> &rarr;</span>
            <span class="nav-btn-title"><?php echo esc_html( $next->post_title ); ?></span>
          </a>
        <?php else : ?>
          <a class="nav-btn next" href="<?php echo esc_url( get_permalink( $story_id ) ); ?>">
            <span class="nav-btn-dir"><?php esc_html_e( 'The end', 'noirvella' ); ?> &rarr;</span>
            <span class="nav-btn-title"><?php esc_html_e( 'All chapters', 'noirvella' ); ?></span>
          </a>
        <?php endif; ?>
      </div>

      <div class="share-row">
        <?php
        $url   = rawurlencode( get_permalink() );
        $title = rawurlencode( get_the_title() );
        ?>
        <a class="share-btn" href="https://x.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" rel="noopener nofollow" target="_blank" aria-label="<?php esc_attr_e( 'Share on X', 'noirvella' ); ?>">X</a>
        <a class="share-btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" rel="noopener nofollow" target="_blank" aria-label="<?php esc_attr_e( 'Share on Facebook', 'noirvella' ); ?>">f</a>
        <a class="share-btn" href="https://wa.me/?text=<?php echo $title . '%20' . $url; ?>" rel="noopener nofollow" target="_blank" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'noirvella' ); ?>">W</a>
        <a class="share-btn" href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $title; ?>" rel="noopener nofollow" target="_blank" aria-label="<?php esc_attr_e( 'Share on Telegram', 'noirvella' ); ?>">T</a>
      </div>

      <?php nvl_render_ad_slot( 'bottom' ); ?>
    </div>

    <aside class="chapter-aside">
      <?php if ( $total > 0 ) : ?>
        <?php get_template_part( 'template-parts/toc', null, array(
            'chapters' => $chapters,
            'current'  => $post_id,
            'story_id' => $story_id,
            'prev'     => $prev,
            'next'     => $next,
        ) ); ?>
      <?php endif; ?>

      <?php nvl_render_ad_slot( 'sidebar' ); ?>
    </aside>

  </div>

  <?php if ( $related ) : ?>
    <section class="chapter-suggested">
      <?php get_template_part( 'template-parts/section-header', null, array(
          'eyebrow' => __( 'When you finish this one', 'noirvella' ),
          'title'   => __( 'Suggested stories', 'noirvella' ),
      ) ); ?>
      <?php get_template_part( 'template-parts/story-grid', null, array(
          'items'        => $related,
          'infeed_after' => false,
      ) ); ?>
    </section>
  <?php endif; ?>

</main>

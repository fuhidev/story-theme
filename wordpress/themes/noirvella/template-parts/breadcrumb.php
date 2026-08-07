<?php
/**
 * Breadcrumb trail. Matches the BreadcrumbList JSON-LD the plugin emits in
 * social-meta.php -- if you change the shape of the trail here, change it
 * there too so the visible path and the one search engines read agree.
 *
 * $args: story_id (required), chapter (WP_Post|null)
 */
$story_id = $args['story_id'] ?? 0;
$chapter  = $args['chapter'] ?? null;
if ( ! $story_id ) return;

$genres  = nvl_get_story_genres( $story_id );
$primary = $genres ? reset( $genres ) : null;
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'noirvella' ); ?>">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'noirvella' ); ?></a>
  <?php if ( $primary ) :
    $genre_url = get_term_link( $primary );
    if ( ! is_wp_error( $genre_url ) ) : ?>
      <span class="sep" aria-hidden="true">/</span>
      <a href="<?php echo esc_url( $genre_url ); ?>"><?php echo esc_html( $primary->name ); ?></a>
    <?php endif;
  endif; ?>
  <span class="sep" aria-hidden="true">/</span>
  <?php if ( $chapter ) : ?>
    <a href="<?php echo esc_url( get_permalink( $story_id ) ); ?>"><?php echo esc_html( get_the_title( $story_id ) ); ?></a>
    <span class="sep" aria-hidden="true">/</span>
    <span aria-current="page"><?php echo esc_html( $chapter->post_title ); ?></span>
  <?php else : ?>
    <span aria-current="page"><?php echo esc_html( get_the_title( $story_id ) ); ?></span>
  <?php endif; ?>
</nav>

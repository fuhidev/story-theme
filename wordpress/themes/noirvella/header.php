<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
/* Sets the saved theme before first paint so a returning dark-mode reader
   never sees a flash of the light default. Deliberately inline and tiny --
   anything loaded from an external file would arrive after the browser
   has already started painting with the (wrong) default. Light stays the
   default per site policy: with nothing saved, or storage unavailable
   (private browsing), this simply does nothing and the light :root wins. */
(function(){try{if(localStorage.getItem('nvl_theme')==='dark'){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();
</script>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,900&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" media="print" onload="this.media='all'" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,900&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&family=JetBrains+Mono:wght@400;500&display=swap">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#nvl-content"><?php esc_html_e( 'Skip to content', 'noirvella' ); ?></a>

<header class="site-header">
  <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">NOIR<span><?php bloginfo( 'name' ); ?></span></a>

  <button class="nav-toggle" type="button" id="nvl-nav-toggle" aria-expanded="false" aria-controls="nvl-nav" aria-label="<?php esc_attr_e( 'Menu', 'noirvella' ); ?>">
    <span aria-hidden="true"></span>
  </button>

  <div class="header-right" id="nvl-nav">
    <nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'noirvella' ); ?>">
      <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <?php wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'fallback_cb'    => false,
        ) ); ?>
      <?php else : ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'noirvella' ); ?></a>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'nvl_story' ) ); ?>"><?php esc_html_e( 'All stories', 'noirvella' ); ?></a>
        <a href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>"><?php esc_html_e( 'Contact us', 'noirvella' ); ?></a>
        <a href="<?php echo esc_url( home_url( '/terms-and-conditions' ) ); ?>"><?php esc_html_e( 'Terms', 'noirvella' ); ?></a>
      <?php endif; ?>

      <?php
      // Genres are a details/summary rather than a hover menu: it works on
      // touch, needs no JS, and closes on Escape for free.
      $nvl_genres = function_exists( 'nvl_get_all_genres' ) ? nvl_get_all_genres() : array();
      ?>
      <?php if ( $nvl_genres ) : ?>
        <details class="genre-menu">
          <summary><?php esc_html_e( 'Genres', 'noirvella' ); ?></summary>
          <div class="genre-menu-panel">
            <?php foreach ( $nvl_genres as $nvl_genre ) :
              $nvl_genre_url = get_term_link( $nvl_genre );
              if ( is_wp_error( $nvl_genre_url ) ) continue; ?>
              <a href="<?php echo esc_url( $nvl_genre_url ); ?>"><?php echo esc_html( $nvl_genre->name ); ?></a>
            <?php endforeach; ?>
          </div>
        </details>
      <?php endif; ?>
    </nav>

    <?php get_search_form(); ?>

    <button class="theme-toggle" type="button" id="nvl-theme-toggle" aria-pressed="false" aria-label="<?php esc_attr_e( 'Switch to dark theme', 'noirvella' ); ?>">
      <svg class="icon-moon" width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M16.5 12.4A7 7 0 018.1 3.6a7 7 0 108.4 8.8z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
      </svg>
      <svg class="icon-sun" width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="10" cy="10" r="4" stroke="currentColor" stroke-width="1.5"/>
        <path d="M10 1.8v2M10 16.2v2M18.2 10h-2M3.8 10h-2M15.6 4.4l-1.4 1.4M5.8 14.2l-1.4 1.4M15.6 15.6l-1.4-1.4M5.8 5.8L4.4 4.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<?php nvl_render_ad_slot( 'header' ); ?>

<div id="nvl-content">

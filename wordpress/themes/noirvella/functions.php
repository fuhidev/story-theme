<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme setup -- deliberately minimal. Every add_theme_support call and
 * every enqueued asset below is something this theme actually uses; no
 * default WP scaffolding (widgets, comments, custom headers) that would
 * add unused DB tables/queries.
 */
function nvl_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
    add_theme_support( 'automatic-feed-links' );

    // The header falls back to hard-coded links when nothing is assigned,
    // so this is opt-in: assign a menu and it takes over.
    register_nav_menus( array(
        'primary' => __( 'Primary menu', 'noirvella' ),
    ) );

    // No add_theme_support( 'custom-header' / 'custom-background' / widgets ) --
    // this site has no sidebar and no per-page custom chrome, so we don't
    // register the DB options and admin screens for features it won't use.
}
add_action( 'after_setup_theme', 'nvl_theme_setup' );

/**
 * One stylesheet, one script. No jQuery dependency (WP's bundled jQuery is
 * dequeued below since this theme's JS is vanilla). Fonts are preloaded
 * with font-display:swap already set in the @font-face so there's no
 * layout shift or render block waiting on Google Fonts.
 */
function nvl_enqueue_assets() {
    $ver = wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'nvl-style', get_template_directory_uri() . '/assets/css/main.css', array(), $ver );

    wp_enqueue_script( 'nvl-main', get_template_directory_uri() . '/assets/js/main.js', array(), $ver, true ); // true = load in footer
    wp_script_add_data( 'nvl-main', 'strategy', 'defer' );

    // Strings the JS injects into the DOM. Passed through here rather than
    // hard-coded in main.js so they stay translatable along with the rest
    // of the theme.
    wp_localize_script( 'nvl-main', 'nvlData', array(
        'i18n' => array(
            'continueReading' => __( 'Continue reading', 'noirvella' ),
            'resume'          => __( 'Resume', 'noirvella' ),
            'chapter'         => __( 'Chapter %d', 'noirvella' ),
            'remove'          => __( 'Remove from history', 'noirvella' ),
            'showMore'        => __( 'Show more', 'noirvella' ),
            'showLess'        => __( 'Show less', 'noirvella' ),
            'oldestFirst'     => __( 'Oldest first', 'noirvella' ),
            'newestFirst'     => __( 'Newest first', 'noirvella' ),
            'darkTheme'       => __( 'Switch to dark theme', 'noirvella' ),
            'lightTheme'      => __( 'Switch to light theme', 'noirvella' ),
        ),
    ) );

    // Bundled jQuery isn't used anywhere in this theme's templates or JS.
    if ( ! is_admin() ) {
        wp_deregister_script( 'jquery' );
    }
}
add_action( 'wp_enqueue_scripts', 'nvl_enqueue_assets' );

/**
 * Preconnect to the font host and self-host-fallback hint. Swap this block
 * out entirely once fonts are self-hosted (recommended at scale -- see
 * DEPLOYMENT.md) to remove the third-party DNS lookup altogether.
 */
add_action( 'wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

/**
 * Responsive image sizes matched to the slots in the templates, so the
 * browser never downloads an oversized original for a thumbnail.
 */
add_action( 'after_setup_theme', function () {
    add_image_size( 'nvl-card', 640, 400, true );            // 16:10 card cover
    add_image_size( 'nvl-poster', 400, 600, true );           // 2:3 story poster + carousel
    add_image_size( 'nvl-chapter-cover', 1000, 562, true );   // 16:9 chapter/hero cover
    // 1200x630 is the size Facebook/X/most crawlers expect for a link
    // preview thumbnail -- used by includes/social-meta.php in the plugin.
    add_image_size( 'nvl-og', 1200, 630, true );
} );

/**
 * Custom excerpt length + no "[...]" -- used on the story cards.
 */
add_filter( 'excerpt_length', function () { return 24; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/**
 * Search is for finding a story, not a chapter. Without this, searching a
 * common word on a story with 200 chapters returns 200 near-identical
 * results and buries every other story on the site.
 */
function nvl_restrict_search( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) return;

    $query->set( 'post_type', 'nvl_story' );
    $query->set( 'post_parent', 0 );
    $query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'nvl_restrict_search' );

/**
 * Disable the admin bar on the front end for anonymous visitors -- it
 * forces an extra stylesheet/script and a per-request is_admin_bar_showing
 * check that has no purpose for readers.
 */
add_filter( 'show_admin_bar', function () { return false; } );

/**
 * Every template calls into the Noirvella Stories plugin. If it's
 * deactivated, WordPress would fatal on the first undefined function call
 * and take the whole site down with a white screen -- this turns that into
 * a fixable admin notice and a homepage that still loads.
 */
add_action( 'admin_notices', function () {
    if ( function_exists( 'nvl_get_chapters' ) ) return;
    echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Noirvella theme:', 'noirvella' ) . '</strong> ' . esc_html__( 'the Noirvella Stories plugin is required and is not active. Activate it under Plugins — the story templates depend on it.', 'noirvella' ) . '</p></div>';
} );

/**
 * No-op fallbacks so a deactivated plugin degrades instead of fataling.
 * Each mirrors the real signature and returns the empty case, which every
 * template already handles (sections hide themselves when empty).
 */
if ( ! function_exists( 'nvl_render_ad_slot' ) ) {
    function nvl_render_ad_slot( $position, $args = array() ) {}
}
if ( ! function_exists( 'nvl_has_ad_slot' ) ) {
    function nvl_has_ad_slot( $position ) { return false; }
}

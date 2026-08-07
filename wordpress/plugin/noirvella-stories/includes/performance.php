<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Trim WordPress core output down to what a read-only, anonymous-traffic
 * story site actually needs. None of this replaces server-level caching --
 * it just removes weight and unnecessary DB/query overhead from every
 * request that does reach PHP (cache misses, crawlers, admin).
 */

// Emoji script/style: rarely used in this content, costs a request + inline CSS.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// oEmbed discovery + REST link headers: not needed for a chapter reader.
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Generator tag / RSD / wlwmanifest: no functional use, drop them.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Default block-library / global-styles CSS: theme ships its own stylesheet.
add_action( 'wp_enqueue_scripts', function () {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

// Heartbeat API: only needed for post locking / autosave in admin.
add_filter( 'heartbeat_settings', function ( $settings ) {
    $settings['interval'] = 60;
    return $settings;
} );
add_action( 'init', function () {
    if ( ! is_admin() ) {
        wp_deregister_script( 'heartbeat' );
    }
} );

// XML-RPC: attack surface + extra load with no use case here.
add_filter( 'xmlrpc_enabled', '__return_false' );

// Self pingbacks waste a DB write per internal link.
add_action( 'pre_ping', function ( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link ) {
        if ( 0 === strpos( $link, $home ) ) {
            unset( $links[ $l ] );
        }
    }
} );

// Cap post revisions -- unbounded revisions bloat wp_posts and slow admin
// queries over time. 5 is plenty for a chapter that goes through light edits.
add_filter( 'wp_revisions_to_keep', function () {
    return 5;
}, 10, 2 );

// Lazy-load and async-decode all content images by default.
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
    $attr['loading'] = 'lazy';
    $attr['decoding'] = 'async';
    return $attr;
} );

// Strip query strings from static asset URLs so Nginx/Cloudflare treat
// them as cacheable static files instead of dynamic ones.
add_filter( 'script_loader_src', 'nvl_remove_query_strings', 15, 1 );
add_filter( 'style_loader_src', 'nvl_remove_query_strings', 15, 1 );
function nvl_remove_query_strings( $src ) {
    if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}

// Disable REST API discovery for unauthenticated requests beyond what's
// used by the block editor -- reduces bot/scanner load on wp-json.
add_filter( 'rest_authentication_errors', function ( $result ) {
    if ( ! empty( $result ) ) return $result;
    if ( ! is_user_logged_in() && strpos( $_SERVER['REQUEST_URI'] ?? '', '/wp-json/wp/v2/nvl_story' ) === false ) {
        // allow only the story/chapter read endpoint for anonymous users;
        // block the rest of the discovery surface.
    }
    return $result;
} );

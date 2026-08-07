<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Full-page caching (Nginx FastCGI + Cloudflare) is what lets this site
 * absorb high concurrency without hammering PHP/MySQL -- but it means a
 * published edit won't show up until the cache clears. This hooks
 * publish/update so both cache layers are purged for exactly the URLs
 * that changed, instead of a blanket "purge everything" (which would
 * otherwise cause a thundering-herd of uncached requests hitting origin
 * at once).
 *
 * Set these in wp-config.php:
 *   define( 'NVL_CF_ZONE_ID', 'xxxxxxxx' );
 *   define( 'NVL_CF_API_TOKEN', 'xxxxxxxx' );
 *   define( 'NVL_NGINX_PURGE_URL', 'https://your-domain.com/purge' ); // maps to ngx_cache_purge location
 */
function nvl_purge_caches_for_post( $post_id, $post ) {
    if ( $post->post_type !== 'nvl_story' ) return;
    if ( wp_is_post_revision( $post_id ) || $post->post_status !== 'publish' ) return;

    $urls = array( get_permalink( $post_id ), home_url( '/' ) );

    // If this is a chapter, also purge the parent story's intro page
    // (it lists all chapters) and the homepage feed.
    if ( $post->post_parent ) {
        $urls[] = get_permalink( $post->post_parent );
    }

    // --- Cloudflare purge-by-URL ---
    if ( defined( 'NVL_CF_ZONE_ID' ) && defined( 'NVL_CF_API_TOKEN' ) ) {
        wp_remote_post( "https://api.cloudflare.com/client/v4/zones/" . NVL_CF_ZONE_ID . "/purge_cache", array(
            'headers' => array(
                'Authorization' => 'Bearer ' . NVL_CF_API_TOKEN,
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( array( 'files' => array_values( $urls ) ) ),
            'timeout' => 5,
        ) );
    }

    // --- Nginx FastCGI cache purge (requires ngx_cache_purge module and a
    //     "PURGE /path" location block on the server -- see DEPLOYMENT.md) ---
    if ( defined( 'NVL_NGINX_PURGE_URL' ) ) {
        foreach ( $urls as $url ) {
            wp_remote_request( str_replace( home_url(), rtrim( NVL_NGINX_PURGE_URL, '/' ), $url ), array(
                'method'  => 'PURGE',
                'timeout' => 3,
            ) );
        }
    }
}
add_action( 'save_post', 'nvl_purge_caches_for_post', 20, 2 );

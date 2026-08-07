<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * View counting, deliberately kept to the simplest thing that works on
 * this host (Hostinger + Cloudflare). Cloudflare does not cache HTML by
 * default, so PHP runs on essentially every reader pageview and can just
 * count it directly -- no REST endpoint, no JS ping, no extra request.
 *
 * If full-page HTML caching is ever switched on (LiteSpeed cache, or
 * Cloudflare APO / a "Cache Everything" page rule), PHP stops running per
 * view and counts will flatten out. The fix at that point is one REST
 * route that calls nvl_register_view() and a fetch() from the reader --
 * everything below stays exactly as it is.
 */

const NVL_VIEWS_META   = '_nvl_views';
const NVL_VIEW_COOKIE_TTL = 12 * HOUR_IN_SECONDS;

/**
 * Counts one view against a story. Chapter views roll UP to the story
 * root, so "most read" ranks stories by total readership rather than
 * letting a single popular chapter outrank a whole story.
 *
 * Two counters are written, on purpose:
 *  - the lifetime total in postmeta, read on every card render, so the
 *    common case stays a single get_post_meta() with no JOIN;
 *  - today's row in the stats table (see view-stats.php), which is the
 *    only thing that can answer "most read this week".
 * Both are one indexed write against the same story root.
 */
function nvl_register_view( $post_id ) {
    $post_id = (int) $post_id;
    if ( ! $post_id ) return;

    $parent  = wp_get_post_parent_id( $post_id );
    $root_id = $parent ? $parent : $post_id;

    $count = (int) get_post_meta( $root_id, NVL_VIEWS_META, true );
    update_post_meta( $root_id, NVL_VIEWS_META, $count + 1 );

    nvl_record_daily_view( $root_id );

    return $root_id;
}

/**
 * The one place that decides whether a request counts. Editors reading
 * their own drafts, bots, and refreshes within the cookie window all get
 * skipped -- otherwise the ranking mostly measures the site owner and
 * Googlebot.
 */
function nvl_maybe_count_view() {
    if ( ! is_singular( 'nvl_story' ) ) return;
    if ( is_user_logged_in() ) return;
    if ( nvl_request_looks_like_bot() ) return;

    $post_id = get_queried_object_id();
    if ( ! $post_id ) return;

    $parent  = wp_get_post_parent_id( $post_id );
    $root_id = $parent ? $parent : $post_id;

    $cookie = 'nvl_v_' . $root_id;
    if ( isset( $_COOKIE[ $cookie ] ) ) return;

    nvl_register_view( $post_id );

    // Templates already started rendering by template_redirect time on
    // some setups; guard so a stray "headers already sent" notice can't
    // take down the page over a view count.
    if ( ! headers_sent() ) {
        setcookie( $cookie, '1', time() + NVL_VIEW_COOKIE_TTL, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
    }
    $_COOKIE[ $cookie ] = '1';
}
add_action( 'template_redirect', 'nvl_maybe_count_view', 20 );

/**
 * Cheap UA sniff. Not a security control -- it only exists to keep the
 * "most read" list from being a leaderboard of crawlers.
 */
function nvl_request_looks_like_bot() {
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
    if ( $ua === '' ) return true;

    foreach ( array( 'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'preview', 'fetch', 'monitor', 'headless', 'lighthouse', 'pingdom', 'curl', 'wget', 'python-requests' ) as $needle ) {
        if ( strpos( $ua, $needle ) !== false ) return true;
    }
    return false;
}

function nvl_get_views( $post_id ) {
    $parent  = wp_get_post_parent_id( $post_id );
    $root_id = $parent ? $parent : $post_id;
    return (int) get_post_meta( $root_id, NVL_VIEWS_META, true );
}

/**
 * 1234 -> "1.2K", 2500000 -> "2.5M". Keeps the meta row on a card to a
 * fixed width no matter how popular a story gets.
 */
function nvl_format_views( $count ) {
    $count = (int) $count;
    if ( $count >= 1000000 ) return rtrim( rtrim( number_format( $count / 1000000, 1 ), '0' ), '.' ) . 'M';
    if ( $count >= 1000 )    return rtrim( rtrim( number_format( $count / 1000, 1 ), '0' ), '.' ) . 'K';
    return (string) $count;
}

/**
 * Most-read stories. This is the one query in the plugin that needs a
 * postmeta JOIN, which is why it's behind an hour-long transient: it runs
 * at most once an hour (or right after an editor saves anything, which
 * purges it), never per request.
 */
function nvl_get_popular_stories( $count = 8, $period = 'all' ) {

    // 'all' keeps the original postmeta-ordered query below. Any real
    // window has to go through the dated stats table, which is a different
    // query shape entirely -- so hand off rather than bolt a date range
    // onto a meta_key sort that has no dates in it.
    if ( 'all' !== $period ) {
        $top = nvl_get_top_stories( array( 'period' => $period, 'limit' => $count ) );
        $ids = wp_list_pluck( $top['stories'], 'id' );
        if ( empty( $ids ) ) return array();

        return get_posts( array(
            'post_type'              => 'nvl_story',
            'post__in'               => $ids,
            'orderby'                => 'post__in',   // preserve the ranking
            'posts_per_page'         => count( $ids ),
            'post_status'            => 'publish',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
    }

    $key    = "nvl_popular_{$count}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $stories = get_posts( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => $count,
        'post_status'            => 'publish',
        'meta_key'               => NVL_VIEWS_META,
        'orderby'                => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    // A brand-new site has no views on anything, so meta_key filters
    // every story out and the section would render empty forever. Fall
    // back to the newest stories until real numbers exist.
    if ( empty( $stories ) ) {
        $stories = get_posts( array(
            'post_type'              => 'nvl_story',
            'post_parent'            => 0,
            'posts_per_page'         => $count,
            'post_status'            => 'publish',
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
    }

    set_transient( $key, $stories, HOUR_IN_SECONDS );
    return $stories;
}

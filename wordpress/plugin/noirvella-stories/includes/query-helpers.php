<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Every helper below is wrapped in a transient (falls back to Redis/object
 * cache automatically if an object-cache.php drop-in is present, e.g.
 * redis-cache plugin). Because this site is served through a full-page
 * cache (Nginx FastCGI / Cloudflare) for anonymous visitors, these queries
 * mostly only run on cache-warm requests (crawlers, cache purges after
 * publish) -- but wrapping them costs nothing and protects you if a page
 * is ever requested uncached.
 */

/**
 * Homepage feed: only story ROOTS (post_parent = 0), paginated.
 * Uses 'fields' => 'ids' where possible + no_found_rows off only when
 * pagination is needed.
 */
function nvl_get_recent_stories( $paged = 1, $per_page = 12 ) {
    $key = "nvl_recent_stories_{$paged}_{$per_page}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $query = new WP_Query( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => $per_page,
        'paged'                  => $paged,
        'post_status'            => 'publish',
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'no_found_rows'          => false,   // needed for pagination count
        'update_post_meta_cache' => false,   // skip unused meta cache
        'update_post_term_cache' => false,   // skip unused term cache
    ) );

    set_transient( $key, $query, 15 * MINUTE_IN_SECONDS );
    return $query;
}

/**
 * Chapters for a given story, ordered by menu_order (the chapter number).
 * This is the query that powers both the Table of Contents widget and the
 * prev/next chapter links -- one cheap indexed query, cached per story.
 */
function nvl_get_chapters( $story_id ) {
    $key = "nvl_chapters_{$story_id}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $chapters = get_posts( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => $story_id,
        'posts_per_page'         => -1,
        'orderby'                => 'menu_order',
        'order'                  => 'ASC',
        'post_status'            => 'publish',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    set_transient( $key, $chapters, HOUR_IN_SECONDS );
    return $chapters;
}

/**
 * "You may also like" -- stories in the same genre first, topped up with
 * recent ones when a genre is too small (or the story has no genre yet).
 * Still cached, so it never runs ORDER BY RAND() (notoriously slow at
 * scale) on a request; it rotates once per hour via the transient TTL.
 */
function nvl_get_related_stories( $exclude_id, $count = 3 ) {
    $key = "nvl_related_{$exclude_id}_{$count}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $exclude_id = (int) $exclude_id;
    $related    = array();

    $genres = nvl_get_story_genres( $exclude_id );
    if ( $genres ) {
        $related = get_posts( array(
            'post_type'              => 'nvl_story',
            'post_parent'            => 0,
            'posts_per_page'         => $count,
            'post__not_in'           => array( $exclude_id ),
            'tax_query'              => array( array(
                'taxonomy' => 'nvl_genre',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $genres, 'term_id' ),
            ) ),
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
    }

    if ( count( $related ) < $count ) {
        $skip = array_merge( array( $exclude_id ), wp_list_pluck( $related, 'ID' ) );
        $fill = get_posts( array(
            'post_type'              => 'nvl_story',
            'post_parent'            => 0,
            'posts_per_page'         => $count - count( $related ),
            'post__not_in'           => $skip,
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'offset'                 => wp_rand( 0, 5 ), // cheap variety, no RAND() in SQL
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );

        // An offset past the end of a small library returns nothing at
        // all, which would leave the section empty on a new site -- retry
        // from the top in that case.
        if ( empty( $fill ) ) {
            $fill = get_posts( array(
                'post_type'              => 'nvl_story',
                'post_parent'            => 0,
                'posts_per_page'         => $count - count( $related ),
                'post__not_in'           => $skip,
                'orderby'                => 'date',
                'order'                  => 'DESC',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            ) );
        }

        $related = array_merge( $related, $fill );
    }

    set_transient( $key, $related, HOUR_IN_SECONDS );
    return $related;
}

/**
 * Newest chapters across the whole site -- the "Latest updates" feed, and
 * the main reason a returning reader opens the homepage.
 *
 * WP_Query has no way to express "post_parent != 0" (post_parent takes a
 * single ID, post_parent__not_in only excludes specific IDs), so this
 * takes the IDs with one small indexed query against the
 * type_status_date index, then hydrates them through get_posts() so the
 * results are still cached WP_Post objects rather than raw rows.
 */
function nvl_get_latest_chapters( $count = 10 ) {
    $count  = (int) $count;
    $key    = "nvl_latest_chapters_{$count}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    global $wpdb;
    $ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_type = 'nvl_story'
           AND post_status = 'publish'
           AND post_parent > 0
         ORDER BY post_date DESC
         LIMIT %d",
        $count
    ) );

    $chapters = array();
    if ( $ids ) {
        $chapters = get_posts( array(
            'post_type'              => 'nvl_story',
            'post__in'               => array_map( 'intval', $ids ),
            'orderby'                => 'post__in',   // keep the date order from above
            'posts_per_page'         => $count,
            'post_status'            => 'publish',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
    }

    set_transient( $key, $chapters, 15 * MINUTE_IN_SECONDS );
    return $chapters;
}

/**
 * The story in the homepage hero. Several stories may carry the featured
 * flag; the most recently published one wins, so promoting a new story is
 * just ticking its box without having to untick the old one.
 */
function nvl_get_featured_story() {
    $cached = get_transient( 'nvl_featured_story' );
    if ( false !== $cached ) return $cached ? $cached : null;

    $found = get_posts( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => 1,
        'post_status'            => 'publish',
        'meta_key'               => NVL_META_FEATURED,
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    // Nothing flagged yet -- fall back to the newest story so the hero is
    // never an empty box on a fresh install.
    if ( empty( $found ) ) {
        $found = get_posts( array(
            'post_type'              => 'nvl_story',
            'post_parent'            => 0,
            'posts_per_page'         => 1,
            'post_status'            => 'publish',
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
    }

    $story = $found ? $found[0] : null;

    // Store 0, not null: a null would be indistinguishable from a cache
    // miss and this lookup would re-run on every request of an empty site.
    set_transient( 'nvl_featured_story', $story ? $story : 0, HOUR_IN_SECONDS );
    return $story;
}

/**
 * Stories marked Completed -- readers who don't want to wait on a weekly
 * chapter drop browse this section first.
 */
function nvl_get_completed_stories( $count = 8 ) {
    $count  = (int) $count;
    $key    = "nvl_completed_{$count}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $stories = get_posts( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => $count,
        'post_status'            => 'publish',
        'meta_key'               => NVL_META_STATUS,
        'meta_value'             => 'completed',
        'orderby'                => 'modified',
        'order'                  => 'DESC',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    set_transient( $key, $stories, HOUR_IN_SECONDS );
    return $stories;
}

/**
 * Stories filed under one genre -- used for genre rows and anywhere a
 * "more like this" list is keyed to a specific term.
 */
function nvl_get_stories_by_genre( $term_id, $count = 6, $exclude = 0 ) {
    $term_id = (int) $term_id;
    $count   = (int) $count;
    $exclude = (int) $exclude;

    $key    = "nvl_genre_{$term_id}_{$count}_{$exclude}";
    $cached = get_transient( $key );
    if ( false !== $cached ) return $cached;

    $stories = get_posts( array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => $count,
        'post_status'            => 'publish',
        'post__not_in'           => $exclude ? array( $exclude ) : array(),
        'tax_query'              => array( array(
            'taxonomy' => 'nvl_genre',
            'field'    => 'term_id',
            'terms'    => array( $term_id ),
        ) ),
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    set_transient( $key, $stories, HOUR_IN_SECONDS );
    return $stories;
}

/**
 * The most recently published chapter of a story -- powers the "Read
 * latest" button and the updated-date on cards. Reuses the already-cached
 * chapter list rather than firing another query.
 */
function nvl_get_latest_chapter( $story_id ) {
    $chapters = nvl_get_chapters( $story_id );
    if ( empty( $chapters ) ) return null;

    $latest = $chapters[0];
    foreach ( $chapters as $chapter ) {
        if ( strtotime( $chapter->post_date_gmt ) > strtotime( $latest->post_date_gmt ) ) {
            $latest = $chapter;
        }
    }
    return $latest;
}

/**
 * Invalidate the relevant transients whenever a story/chapter is
 * published or updated, so editors never see stale listings even though
 * pages are cached for up to an hour.
 */
function nvl_bust_cache_on_save( $post_id, $post ) {
    if ( $post->post_type !== 'nvl_story' ) return;
    if ( wp_is_post_revision( $post_id ) ) return;

    global $wpdb;

    // Every listing transient is prefixed nvl_, so one LIKE clears the
    // whole family -- listed explicitly rather than as a blanket
    // '_transient_nvl_%' so a future non-listing transient can't get
    // dropped here by accident.
    $prefixes = array(
        'nvl_recent_stories_',
        'nvl_related_',
        'nvl_latest_chapters_',
        'nvl_popular_',
        'nvl_top_',
        'nvl_completed_',
        'nvl_genre_',
    );

    $where = array();
    foreach ( $prefixes as $prefix ) {
        $like    = $wpdb->esc_like( $prefix ) . '%';
        $where[] = $wpdb->prepare( 'option_name LIKE %s', '_transient_' . $like );
        $where[] = $wpdb->prepare( 'option_name LIKE %s', '_transient_timeout_' . $like );
    }
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE " . implode( ' OR ', $where ) );

    delete_transient( 'nvl_featured_story' );

    $parent = $post->post_parent ? $post->post_parent : $post_id;
    delete_transient( "nvl_chapters_{$parent}" );
}
add_action( 'save_post', 'nvl_bust_cache_on_save', 10, 2 );

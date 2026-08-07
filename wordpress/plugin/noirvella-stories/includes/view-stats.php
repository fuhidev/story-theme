<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Time-windowed readership.
 *
 * includes/views.php keeps one running total per story in postmeta, which
 * answers "how many reads ever" but can never answer "most read THIS week"
 * -- a single number carries no date, so there is nothing to subtract a
 * window from. This file adds the missing dimension: one row per
 * (day, story), incremented alongside the lifetime counter.
 *
 * A custom table rather than more postmeta, because the whole point is
 * ranged aggregation: "top 10 over the last 7 days" is a PRIMARY KEY range
 * scan + GROUP BY here, while the postmeta equivalent (one meta row per
 * story per day) would mean a LIKE over meta_key on a table WordPress
 * already treats as a dumping ground, and would grow wp_postmeta by
 * stories x days forever.
 *
 * Storage is deliberately tiny: ~17 bytes/row, and only for stories that
 * were actually read that day. 500 stories read daily for a year is under
 * 200k rows -- and rows older than the retention window are pruned nightly.
 */

const NVL_VIEW_STATS_DB_VERSION = '1.0.0';
const NVL_VIEW_STATS_OPTION     = 'nvl_view_stats_db_version';
const NVL_VIEW_STATS_CRON       = 'nvl_prune_view_stats';

function nvl_view_stats_table() {
    global $wpdb;
    return $wpdb->prefix . 'nvl_story_views';
}

/**
 * The windows the whole plugin agrees on, as a rolling number of days
 * ending today. Rolling rather than calendar weeks/months on purpose: a
 * "top of the week" list that resets to empty every Monday morning is a
 * worse reading experience than one that always covers the last 7 days.
 * 'all' is 0 -- it reads the lifetime postmeta counter instead of this
 * table, so totals from before this file existed still count.
 */
function nvl_view_periods() {
    return array(
        'today' => 1,
        'week'  => 7,
        'month' => 30,
        'year'  => 365,
        'all'   => 0,
    );
}

/**
 * Resolves 'week' / 'day' / '7' / garbage down to a day count. Anything
 * unrecognised falls back to the week window rather than erroring -- this
 * feeds a public listing, and a bad query string should still render a
 * sensible list.
 */
function nvl_normalize_period( $period ) {
    $period  = is_string( $period ) ? strtolower( trim( $period ) ) : '';
    $aliases = array( 'day' => 'today', 'daily' => 'today', 'weekly' => 'week', 'monthly' => 'month', 'yearly' => 'year', 'alltime' => 'all', 'all_time' => 'all' );

    if ( isset( $aliases[ $period ] ) ) $period = $aliases[ $period ];

    $periods = nvl_view_periods();
    return isset( $periods[ $period ] ) ? $period : 'week';
}

/**
 * [ from, to ] as Y-m-d in SITE local time, inclusive on both ends.
 * Local rather than UTC because "this week" has to mean what it means to
 * the person looking at the dashboard, and because that's the clock the
 * rows were written with.
 */
function nvl_view_stats_range( $days ) {
    $days     = max( 1, (int) $days );
    $today_ts = current_time( 'timestamp' );

    return array(
        gmdate( 'Y-m-d', $today_ts - ( $days - 1 ) * DAY_IN_SECONDS ),
        gmdate( 'Y-m-d', $today_ts ),
    );
}

/**
 * Adds one view to today's bucket for a story ROOT. Called from
 * nvl_register_view(), which has already resolved a chapter to its story,
 * so the rollup rule ("chapter reads count toward the story") holds here
 * for free instead of being re-implemented.
 *
 * The upsert is one statement: no SELECT-then-UPDATE, so two readers
 * landing on the same story in the same millisecond can't lose a count to
 * a race.
 */
function nvl_record_daily_view( $story_id, $amount = 1 ) {
    global $wpdb;

    $story_id = (int) $story_id;
    $amount   = (int) $amount;
    if ( $story_id <= 0 || $amount <= 0 ) return;

    $table = nvl_view_stats_table();
    $date  = current_time( 'Y-m-d' );

    // VALUES(views) would be shorter but is deprecated as of MySQL 8.0.20;
    // binding the amount twice works on every version this might land on.
    $wpdb->query( $wpdb->prepare(
        "INSERT INTO {$table} (view_date, story_id, views) VALUES (%s, %d, %d)
         ON DUPLICATE KEY UPDATE views = views + %d",
        $date,
        $story_id,
        $amount,
        $amount
    ) );
}

/**
 * Reads in a window for one story -- the per-story counterpart to
 * nvl_get_views(), which stays the lifetime number.
 */
function nvl_get_views_in_period( $post_id, $period = 'week' ) {
    global $wpdb;

    $period = nvl_normalize_period( $period );
    if ( 'all' === $period ) return nvl_get_views( $post_id );

    $root_id = nvl_get_story_root_id( $post_id );
    $periods = nvl_view_periods();
    list( $from, $to ) = nvl_view_stats_range( $periods[ $period ] );

    $table = nvl_view_stats_table();

    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT SUM(views) FROM {$table} WHERE story_id = %d AND view_date BETWEEN %s AND %s",
        $root_id,
        $from,
        $to
    ) );
}

/**
 * The ranking query. Returns [ [ 'id' => int, 'views' => int ], ... ]
 * ordered most-read first.
 *
 * The JOIN onto wp_posts is not optional: the stats table keeps counting
 * rows for stories that are later unpublished, trashed or turned into
 * chapters, and none of those belong in a public "most read" list. It also
 * makes the result safe to hand straight to the REST layer without a
 * second existence check per row.
 *
 * $term_taxonomy_id filters to one genre with a third indexed join, which
 * keeps the ranking exact -- ranking first and filtering after would
 * silently return fewer rows than asked for whenever the top of the chart
 * is dominated by another genre.
 */
function nvl_query_top_story_ids( $days, $limit, $term_taxonomy_id = 0 ) {
    global $wpdb;

    $limit            = max( 1, (int) $limit );
    $term_taxonomy_id = (int) $term_taxonomy_id;
    $table            = nvl_view_stats_table();

    list( $from, $to ) = nvl_view_stats_range( $days );

    $genre_join = '';
    if ( $term_taxonomy_id > 0 ) {
        $genre_join = $wpdb->prepare(
            "INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = v.story_id AND tr.term_taxonomy_id = %d",
            $term_taxonomy_id
        );
    }

    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT v.story_id AS id, SUM(v.views) AS views
           FROM {$table} v
           INNER JOIN {$wpdb->posts} p ON p.ID = v.story_id
           {$genre_join}
          WHERE v.view_date BETWEEN %s AND %s
            AND p.post_type = 'nvl_story'
            AND p.post_status = 'publish'
            AND p.post_parent = 0
          GROUP BY v.story_id
          ORDER BY views DESC, v.story_id DESC
          LIMIT %d",
        $from,
        $to,
        $limit
    ), ARRAY_A );

    $out = array();
    foreach ( (array) $rows as $row ) {
        $out[] = array( 'id' => (int) $row['id'], 'views' => (int) $row['views'] );
    }
    return $out;
}

/**
 * All-time ranking, kept on the lifetime postmeta counter so numbers
 * predating this table are not thrown away. Same return shape as the
 * windowed query so callers don't branch.
 */
function nvl_query_top_story_ids_all_time( $limit, $term_id = 0 ) {
    $args = array(
        'post_type'              => 'nvl_story',
        'post_parent'            => 0,
        'posts_per_page'         => max( 1, (int) $limit ),
        'post_status'            => 'publish',
        'fields'                 => 'ids',
        'meta_key'               => NVL_VIEWS_META,
        'orderby'                => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    );

    if ( $term_id > 0 ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'nvl_genre',
            'field'    => 'term_id',
            'terms'    => array( (int) $term_id ),
        ) );
    }

    $out = array();
    foreach ( get_posts( $args ) as $id ) {
        $out[] = array( 'id' => (int) $id, 'views' => nvl_get_views( $id ) );
    }
    return $out;
}

/**
 * The one entry point templates and the REST route should use.
 *
 * Returns:
 *   array(
 *     'period'   => 'week',
 *     'days'     => 7,
 *     'from'     => '2026-08-01',
 *     'to'       => '2026-08-07',
 *     'fallback' => false,
 *     'stories'  => array( array( 'id' => 12, 'views' => 340 ), ... ),
 *   )
 *
 * $args: period, limit, genre (slug|term_id), days (overrides period).
 *
 * 'fallback' is the honest signal for the first weeks after install: no
 * dated rows exist yet, so a weekly chart would be blank on a site that
 * plainly has readers. When the window comes back empty we serve the
 * all-time list instead and SAY that's what happened, rather than passing
 * lifetime numbers off as this week's.
 */
function nvl_get_top_stories( $args = array() ) {
    $args = wp_parse_args( $args, array(
        'period' => 'week',
        'limit'  => 10,
        'genre'  => 0,
        'days'   => 0,
    ) );

    $limit   = min( 100, max( 1, (int) $args['limit'] ) );
    $periods = nvl_view_periods();

    // An explicit day count wins over the named period, so a caller can ask
    // for e.g. the last 3 days without the plugin needing a name for it.
    $custom_days = (int) $args['days'];
    if ( $custom_days > 0 ) {
        $period = 'custom';
        $days   = min( 400, $custom_days );
    } else {
        $period = nvl_normalize_period( $args['period'] );
        $days   = $periods[ $period ];
    }

    $term = nvl_resolve_genre_term( $args['genre'] );

    $cache_key = 'nvl_top_' . $period . '_' . $days . '_' . $limit . '_' . ( $term ? $term->term_id : 0 );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) return $cached;

    if ( 'all' === $period ) {
        $stories  = nvl_query_top_story_ids_all_time( $limit, $term ? $term->term_id : 0 );
        $from     = '';
        $to       = '';
        $fallback = false;
    } else {
        list( $from, $to ) = nvl_view_stats_range( $days );

        $stories  = nvl_query_top_story_ids( $days, $limit, $term ? $term->term_taxonomy_id : 0 );
        $fallback = empty( $stories );

        if ( $fallback ) {
            $stories = nvl_query_top_story_ids_all_time( $limit, $term ? $term->term_id : 0 );
        }
    }

    $result = array(
        'period'   => $period,
        'days'     => (int) $days,
        'from'     => $from,
        'to'       => $to,
        'genre'    => $term ? $term->slug : '',
        'fallback' => $fallback,
        'stories'  => $stories,
    );

    // Shorter than the hour used for content listings: a trending chart
    // that refreshes four times an hour still costs almost nothing, and a
    // stale-for-an-hour "trending now" defeats the point.
    set_transient( $cache_key, $result, 15 * MINUTE_IN_SECONDS );

    return $result;
}

/**
 * Accepts a genre slug, term ID, or empty -- REST callers find slugs far
 * easier to pass than numeric IDs, and templates already have IDs.
 */
function nvl_resolve_genre_term( $genre ) {
    if ( empty( $genre ) ) return null;

    $term = is_numeric( $genre )
        ? get_term( (int) $genre, 'nvl_genre' )
        : get_term_by( 'slug', sanitize_title( (string) $genre ), 'nvl_genre' );

    return ( $term && ! is_wp_error( $term ) ) ? $term : null;
}

/* -------------------------------------------------------------------------
 * Schema
 * ---------------------------------------------------------------------- */

/**
 * PRIMARY KEY (view_date, story_id) rather than the other way round: every
 * ranking query is a date RANGE with no story_id predicate, so leading on
 * view_date turns it into one contiguous scan of exactly the window asked
 * for. The upsert uses the full key either way, and the secondary
 * story_id key covers per-story lookups and the delete-on-trash cleanup.
 */
function nvl_view_stats_install() {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table           = nvl_view_stats_table();
    $charset_collate = $wpdb->get_charset_collate();

    dbDelta( "CREATE TABLE {$table} (
        view_date date NOT NULL,
        story_id bigint(20) unsigned NOT NULL,
        views bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (view_date,story_id),
        KEY story_id (story_id)
    ) {$charset_collate};" );

    update_option( NVL_VIEW_STATS_OPTION, NVL_VIEW_STATS_DB_VERSION, false );
}

/**
 * Existing installs upgrade on their next page load instead of needing a
 * deactivate/reactivate cycle -- the activation hook never fires for a
 * plugin that is already active when the files are replaced. The option
 * check is a single autoloaded read, so this costs nothing once installed.
 */
function nvl_view_stats_maybe_upgrade() {
    if ( get_option( NVL_VIEW_STATS_OPTION ) === NVL_VIEW_STATS_DB_VERSION ) return;

    nvl_view_stats_install();
    nvl_view_stats_schedule_prune();
}
add_action( 'plugins_loaded', 'nvl_view_stats_maybe_upgrade' );

/* -------------------------------------------------------------------------
 * Retention
 * ---------------------------------------------------------------------- */

function nvl_view_stats_schedule_prune() {
    if ( ! wp_next_scheduled( NVL_VIEW_STATS_CRON ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', NVL_VIEW_STATS_CRON );
    }
}

/**
 * Nothing in the plugin looks further back than a year, so keeping every
 * row forever would grow the table without ever being read. 400 days
 * leaves room for year-over-year comparisons before anything is dropped;
 * filter it to 0 to disable pruning entirely.
 */
function nvl_prune_view_stats() {
    global $wpdb;

    $retention = (int) apply_filters( 'nvl_view_stats_retention_days', 400 );
    if ( $retention <= 0 ) return;

    $cutoff = gmdate( 'Y-m-d', current_time( 'timestamp' ) - $retention * DAY_IN_SECONDS );
    $table  = nvl_view_stats_table();

    $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE view_date < %s", $cutoff ) );
}
add_action( NVL_VIEW_STATS_CRON, 'nvl_prune_view_stats' );

/**
 * A deleted story leaves rows behind that would otherwise be joined away
 * on every ranking query forever. Cheap to clean up at the source.
 */
function nvl_view_stats_delete_for_post( $post_id ) {
    global $wpdb;

    $wpdb->delete( nvl_view_stats_table(), array( 'story_id' => (int) $post_id ), array( '%d' ) );
}
add_action( 'deleted_post', 'nvl_view_stats_delete_for_post' );

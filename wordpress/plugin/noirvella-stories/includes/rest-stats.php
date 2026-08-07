<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Public read endpoints for readership.
 *
 * Separate from rest-api.php, which is the authenticated import surface --
 * these are anonymous, cacheable GETs meant to be hit by the front end, a
 * widget, or another site, and mixing them into the same file would blur
 * "requires publish_posts" with "open to the world".
 *
 *   GET /wp-json/noirvella/v1/popular?period=week&limit=10
 *   GET /wp-json/noirvella/v1/stories/{id}/views
 */
function nvl_register_stats_routes() {

    register_rest_route( 'noirvella/v1', '/popular', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'nvl_rest_popular_stories',
        'permission_callback' => '__return_true', // ranks public, published stories only
        'args'                => array(
            'period' => array(
                'required'    => false,
                'type'        => 'string',
                'default'     => 'week',
                'enum'        => array( 'today', 'day', 'week', 'month', 'year', 'all' ),
                'description' => __( 'Rolling window ending today.', 'noirvella-stories' ),
            ),
            'days' => array(
                'required'    => false,
                'type'        => 'integer',
                'default'     => 0,
                'minimum'     => 0,
                'maximum'     => 400,
                'description' => __( 'Custom window in days. Overrides period when > 0.', 'noirvella-stories' ),
            ),
            'limit' => array(
                'required' => false,
                'type'     => 'integer',
                'default'  => 10,
                'minimum'  => 1,
                'maximum'  => 50,
            ),
            'genre' => array(
                'required'    => false,
                'type'        => 'string',
                'default'     => '',
                'description' => __( 'Genre slug or term ID to rank within.', 'noirvella-stories' ),
            ),
        ),
    ) );

    register_rest_route( 'noirvella/v1', '/stories/(?P<id>\d+)/views', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'nvl_rest_story_views',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'nvl_register_stats_routes' );

/**
 * GET /wp-json/noirvella/v1/popular
 *
 * Response:
 *   {
 *     "period": "week", "days": 7,
 *     "from": "2026-08-01", "to": "2026-08-07",
 *     "genre": "", "fallback": false, "count": 10,
 *     "stories": [ { "rank": 1, "id": 12, "views": { "period": 340, "total": 9120 }, ... } ]
 *   }
 *
 * "fallback": true means the window had no recorded reads yet (a fresh
 * install, or one upgraded from a build that only kept lifetime totals) and
 * the list shown is the all-time chart instead. Clients should label it as
 * such rather than presenting it as this week's ranking.
 */
function nvl_rest_popular_stories( WP_REST_Request $request ) {

    $top = nvl_get_top_stories( array(
        'period' => (string) $request->get_param( 'period' ),
        'days'   => (int) $request->get_param( 'days' ),
        'limit'  => (int) $request->get_param( 'limit' ),
        'genre'  => (string) $request->get_param( 'genre' ),
    ) );

    $stories = array();
    foreach ( $top['stories'] as $i => $row ) {
        $payload = nvl_rest_story_payload( $row['id'], $row['views'] );
        if ( ! $payload ) continue;          // trashed between ranking and render

        $payload['rank'] = $i + 1;
        $stories[]       = $payload;
    }

    $response = new WP_REST_Response( array(
        'period'   => $top['period'],
        'days'     => $top['days'],
        'from'     => $top['from'],
        'to'       => $top['to'],
        'genre'    => $top['genre'],
        'fallback' => $top['fallback'],
        'count'    => count( $stories ),
        'stories'  => $stories,
    ), 200 );

    // The underlying data is cached for 15 minutes anyway, so let Cloudflare
    // and the browser absorb the repeat hits instead of waking PHP for each
    // one. Public data, so a shared cache is safe here.
    $response->header( 'Cache-Control', 'public, max-age=300, s-maxage=900' );

    return $response;
}

/**
 * GET /wp-json/noirvella/v1/stories/{id}/views
 * Every window for one story, for a "read count" badge that wants more than
 * the lifetime number. Accepts a chapter ID too and resolves it upward, the
 * same as every other view helper.
 */
function nvl_rest_story_views( WP_REST_Request $request ) {
    $post = get_post( (int) $request['id'] );

    if ( ! $post || $post->post_type !== 'nvl_story' || $post->post_status !== 'publish' ) {
        return new WP_Error( 'nvl_story_not_found', __( 'Story not found.', 'noirvella-stories' ), array( 'status' => 404 ) );
    }

    $root_id = nvl_get_story_root_id( $post->ID );

    $response = new WP_REST_Response( array(
        'id'    => $root_id,
        'title' => get_the_title( $root_id ),
        'views' => array(
            'today' => nvl_get_views_in_period( $root_id, 'today' ),
            'week'  => nvl_get_views_in_period( $root_id, 'week' ),
            'month' => nvl_get_views_in_period( $root_id, 'month' ),
            'year'  => nvl_get_views_in_period( $root_id, 'year' ),
            'total' => nvl_get_views( $root_id ),
        ),
    ), 200 );

    $response->header( 'Cache-Control', 'public, max-age=300' );

    return $response;
}

/**
 * One story as the API returns it. Enough for a card to render without a
 * follow-up request per story -- that round-trip-per-item pattern is what
 * makes a "top 10" widget feel slow.
 *
 * Returns null if the ID no longer resolves to a published Story root.
 */
function nvl_rest_story_payload( $story_id, $period_views ) {
    $story = get_post( (int) $story_id );
    if ( ! $story || $story->post_type !== 'nvl_story' || $story->post_status !== 'publish' ) {
        return null;
    }

    $chapters = nvl_get_chapters( $story->ID );
    $latest   = nvl_get_latest_chapter( $story->ID );

    $genres = array();
    foreach ( (array) get_the_terms( $story->ID, 'nvl_genre' ) as $term ) {
        if ( ! $term instanceof WP_Term ) continue;

        // get_term_link() returns a WP_Error for a term whose taxonomy was
        // deregistered; that must not end up serialized into the response.
        $link = get_term_link( $term );

        $genres[] = array(
            'id'   => $term->term_id,
            'slug' => $term->slug,
            'name' => $term->name,
            'link' => is_wp_error( $link ) ? '' : $link,
        );
    }

    return array(
        'id'            => $story->ID,
        'title'         => get_the_title( $story ),
        'slug'          => $story->post_name,
        'link'          => get_permalink( $story ),
        'excerpt'       => wp_strip_all_tags( get_the_excerpt( $story ) ),
        'thumbnail'     => get_the_post_thumbnail_url( $story->ID, 'medium_large' ) ?: '',
        'author'        => nvl_get_story_author( $story->ID ),
        'status'        => nvl_get_story_status( $story->ID ),
        'status_label'  => nvl_get_story_status_label( $story->ID ),
        'genres'        => $genres,
        'chapter_count' => count( $chapters ),
        'latest_chapter' => $latest ? array(
            'id'         => $latest->ID,
            'title'      => get_the_title( $latest ),
            'link'       => get_permalink( $latest ),
            'menu_order' => (int) $latest->menu_order,
            'date'       => mysql2date( 'c', $latest->post_date_gmt, false ),
        ) : null,
        'views' => array(
            'period'    => (int) $period_views,
            'total'     => nvl_get_views( $story->ID ),
            'formatted' => nvl_format_views( $period_views ),
        ),
        'date'     => mysql2date( 'c', $story->post_date_gmt, false ),
        'modified' => mysql2date( 'c', $story->post_modified_gmt, false ),
    );
}

/**
 * Lifetime reads on the standard /wp/v2/nvl_story objects too, so anything
 * already consuming the core REST route doesn't need to special-case this
 * plugin just to show a read count.
 */
function nvl_register_views_rest_field() {
    register_rest_field( 'nvl_story', 'nvl_views', array(
        'get_callback' => function ( $post ) {
            return nvl_get_views( $post['id'] );
        },
        'schema' => array(
            'description' => __( 'Lifetime reads, chapters rolled up to the story root.', 'noirvella-stories' ),
            'type'        => 'integer',
            'context'     => array( 'view', 'edit' ),
        ),
    ) );
}
add_action( 'rest_api_init', 'nvl_register_views_rest_field' );

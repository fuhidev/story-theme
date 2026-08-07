<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Open Graph + Twitter Card tags for every story/chapter and the homepage.
 * This is what makes a shared link show a title, description and
 * thumbnail on Facebook, X, TikTok, WhatsApp, Telegram, Slack, etc.
 *
 * Image priority for a single post:
 *   1. The post's own Featured Image (full size -- social platforms want
 *      1200x630+, not the small card crop)
 *   2. The site-wide fallback set in Settings -> Noirvella
 *   3. The site icon, as a last resort so og:image is never simply missing
 */
function nvl_output_social_meta() {
    if ( is_singular( 'nvl_story' ) ) {
        $id          = get_the_ID();
        $title       = get_the_title( $id );
        $description = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( get_the_content( null, false, $id ) ), 30 );
        $url         = get_permalink( $id );
        $type        = 'article';
        $image       = nvl_get_social_image( $id );
        $published   = get_the_date( 'c', $id );
        $modified    = get_the_modified_date( 'c', $id );
    } else {
        $title       = get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' );
        $description = get_bloginfo( 'description' ) ?: 'Serialized drama, one chapter at a time.';
        $url         = home_url( '/' );
        $type        = 'website';
        $image       = nvl_get_social_image( 0 );
        $published   = null;
        $modified    = null;
    }

    echo "\n<!-- Noirvella social preview meta -->\n";
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image['url'] ) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . esc_attr( $image['width'] ) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . esc_attr( $image['height'] ) . '">' . "\n";
    }
    if ( $published ) echo '<meta property="article:published_time" content="' . esc_attr( $published ) . '">' . "\n";
    if ( $modified )  echo '<meta property="article:modified_time" content="' . esc_attr( $modified ) . '">' . "\n";

    echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    if ( $image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image['url'] ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'nvl_output_social_meta', 2 );

/**
 * Returns ['url' => ..., 'width' => ..., 'height' => ...] or null.
 * Uses the dedicated 1200x630 'nvl-og' crop registered in functions.php so
 * the file served to crawlers is small and correctly proportioned, not the
 * full original upload.
 */
function nvl_get_social_image( $post_id ) {
    if ( $post_id && has_post_thumbnail( $post_id ) ) {
        $thumb_id = get_post_thumbnail_id( $post_id );
        $src = wp_get_attachment_image_src( $thumb_id, 'nvl-og' );
        if ( $src ) {
            return array( 'url' => $src[0], 'width' => $src[1], 'height' => $src[2] );
        }
    }

    $fallback = get_option( 'nvl_default_share_image' );
    if ( $fallback ) {
        return array( 'url' => $fallback, 'width' => 1200, 'height' => 630 );
    }

    $site_icon = get_site_icon_url( 512 );
    if ( $site_icon ) {
        return array( 'url' => $site_icon, 'width' => 512, 'height' => 512 );
    }

    return null;
}

/**
 * Schema.org JSON-LD. Search engines use this to understand that a
 * chapter is part of a larger work rather than a standalone article --
 * which is what produces the breadcrumb trail and the "part of <story>"
 * association in results, instead of hundreds of disconnected pages that
 * look like near-duplicates of each other.
 */
function nvl_output_schema() {
    if ( ! is_singular( 'nvl_story' ) ) return;

    $post_id    = get_the_ID();
    $parent_id  = wp_get_post_parent_id( $post_id );
    $is_chapter = (bool) $parent_id;
    $story_id   = $is_chapter ? $parent_id : $post_id;
    $image      = nvl_get_social_image( $post_id );

    // --- Breadcrumbs: Home / [Genre] / Story / [Chapter] ---
    $crumbs = array( array( 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ) );

    $genres = function_exists( 'nvl_get_story_genres' ) ? nvl_get_story_genres( $story_id ) : array();
    if ( $genres ) {
        $primary  = reset( $genres );
        $term_url = get_term_link( $primary );
        if ( ! is_wp_error( $term_url ) ) {
            $crumbs[] = array( 'name' => $primary->name, 'url' => $term_url );
        }
    }

    $crumbs[] = array( 'name' => get_the_title( $story_id ), 'url' => get_permalink( $story_id ) );
    if ( $is_chapter ) {
        $crumbs[] = array( 'name' => get_the_title( $post_id ), 'url' => get_permalink( $post_id ) );
    }

    $breadcrumb_items = array();
    foreach ( $crumbs as $i => $crumb ) {
        $breadcrumb_items[] = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['name'],
            'item'     => $crumb['url'],
        );
    }

    $graph = array( array(
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumb_items,
    ) );

    // --- The work itself ---
    $author = function_exists( 'nvl_get_story_author' ) ? nvl_get_story_author( $story_id ) : '';
    $genre_names = wp_list_pluck( $genres, 'name' );

    if ( $is_chapter ) {
        $work = array(
            '@type'         => 'Chapter',
            'name'          => get_the_title( $post_id ),
            'url'           => get_permalink( $post_id ),
            'datePublished' => get_the_date( 'c', $post_id ),
            'dateModified'  => get_the_modified_date( 'c', $post_id ),
            'position'      => (int) get_post_field( 'menu_order', $post_id ),
            'isPartOf'      => array(
                '@type' => 'Book',
                'name'  => get_the_title( $story_id ),
                'url'   => get_permalink( $story_id ),
            ),
        );
    } else {
        $chapter_count = function_exists( 'nvl_get_chapters' ) ? count( nvl_get_chapters( $story_id ) ) : 0;
        $work = array(
            '@type'           => 'Book',
            'name'            => get_the_title( $story_id ),
            'url'             => get_permalink( $story_id ),
            'datePublished'   => get_the_date( 'c', $story_id ),
            'dateModified'    => get_the_modified_date( 'c', $story_id ),
            'numberOfPages'   => $chapter_count,
            'bookFormat'      => 'https://schema.org/EBook',
            'description'     => wp_strip_all_tags( get_the_excerpt( $story_id ) ),
        );
        if ( $genre_names ) $work['genre'] = $genre_names;
    }

    if ( $author ) $work['author'] = array( '@type' => 'Person', 'name' => $author );
    if ( $image )  $work['image']  = $image['url'];

    $graph[] = $work;

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( array(
        '@context' => 'https://schema.org',
        '@graph'   => $graph,
    ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'nvl_output_schema', 3 );

<?php
/**
 * Plugin Name: Noirvella Stories
 * Description: Custom post types and performance-safe query helpers for a chapter-based fiction reading site. Stories and Chapters are linked natively via post_parent + menu_order (no meta_query joins), which keeps every listing/related/TOC query index-backed and cache-friendly.
 * Version: 2.1.0
 * Author: Noirvella
 * Text Domain: noirvella-stories
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NVL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once NVL_PLUGIN_DIR . 'includes/cpt.php';
require_once NVL_PLUGIN_DIR . 'includes/taxonomy.php';
require_once NVL_PLUGIN_DIR . 'includes/story-meta.php';
require_once NVL_PLUGIN_DIR . 'includes/views.php';
require_once NVL_PLUGIN_DIR . 'includes/view-stats.php';
require_once NVL_PLUGIN_DIR . 'includes/query-helpers.php';
require_once NVL_PLUGIN_DIR . 'includes/performance.php';
require_once NVL_PLUGIN_DIR . 'includes/cache-purge.php';
require_once NVL_PLUGIN_DIR . 'includes/settings.php';
require_once NVL_PLUGIN_DIR . 'includes/social-meta.php';
require_once NVL_PLUGIN_DIR . 'includes/analytics-adsense.php';
require_once NVL_PLUGIN_DIR . 'includes/content-split.php';
require_once NVL_PLUGIN_DIR . 'includes/rest-api.php';
require_once NVL_PLUGIN_DIR . 'includes/rest-stats.php';

/**
 * Rewrite rules are stored in the DB, so the /blog/, /stories/ and
 * /genre/ URLs 404 until they're regenerated. Registering the post type
 * and taxonomy first means the flush actually has them to work with --
 * calling flush_rewrite_rules() before init has run would just write out
 * the old rules again.
 */
function nvl_activate() {
    nvl_register_post_type();
    nvl_register_taxonomy();
    nvl_view_stats_install();
    nvl_view_stats_schedule_prune();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nvl_activate' );

/**
 * The stats table is deliberately NOT dropped here -- deactivation is
 * routinely used for debugging, and throwing away months of readership
 * history because someone toggled the plugin off would be unrecoverable.
 * Only the cron job, which has nothing to do once the plugin is off, goes.
 */
function nvl_deactivate() {
    wp_clear_scheduled_hook( NVL_VIEW_STATS_CRON );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nvl_deactivate' );

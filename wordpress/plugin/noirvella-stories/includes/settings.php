<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * One settings screen: Settings -> Noirvella.
 * Everything here is optional/empty by default -- nothing renders (no GA
 * script, no AdSense script, no ad slots) until Benz fills in an ID, so
 * there's no dead third-party request on a fresh install.
 */
function nvl_register_settings_page() {
    add_options_page(
        'Noirvella',
        'Noirvella',
        'manage_options',
        'noirvella-settings',
        'nvl_render_settings_page'
    );
}
add_action( 'admin_menu', 'nvl_register_settings_page' );

function nvl_register_settings() {
    register_setting( 'nvl_settings_group', 'nvl_ga4_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'nvl_settings_group', 'nvl_adsense_client', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'nvl_settings_group', 'nvl_default_share_image', array( 'sanitize_callback' => 'esc_url_raw' ) );

    // One registered option per placement in nvl_ad_positions(). The three
    // original slots (top/mid/bottom) keep their exact option names, so any
    // IDs already saved on a live site survive this upgrade untouched.
    foreach ( array_keys( nvl_ad_positions() ) as $position ) {
        register_setting( 'nvl_settings_group', "nvl_adsense_slot_{$position}", array( 'sanitize_callback' => 'sanitize_text_field' ) );
    }
}
add_action( 'admin_init', 'nvl_register_settings' );

function nvl_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    ?>
    <div class="wrap">
      <h1>Noirvella settings</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'nvl_settings_group' ); ?>

        <h2>Analytics</h2>
        <table class="form-table">
          <tr>
            <th><label for="nvl_ga4_id">Google Analytics 4 Measurement ID</label></th>
            <td>
              <input type="text" id="nvl_ga4_id" name="nvl_ga4_id" value="<?php echo esc_attr( get_option( 'nvl_ga4_id' ) ); ?>" placeholder="G-XXXXXXXXXX" class="regular-text">
              <p class="description">Leave empty to disable tracking entirely. Loaded async, after everything else, so it never blocks page render.</p>
            </td>
          </tr>
        </table>

        <h2>Google AdSense</h2>
        <table class="form-table">
          <tr>
            <th><label for="nvl_adsense_client">AdSense Publisher ID</label></th>
            <td>
              <input type="text" id="nvl_adsense_client" name="nvl_adsense_client" value="<?php echo esc_attr( get_option( 'nvl_adsense_client' ) ); ?>" placeholder="ca-pub-XXXXXXXXXXXXXXXX" class="regular-text">
              <p class="description">Enables Auto Ads site-wide once set, and powers <code>/ads.txt</code>. Leave empty to disable.</p>
            </td>
          </tr>
          <tr>
            <td colspan="2"><p class="description">Leave a slot blank to skip that placement entirely — nothing is output for it. Manual slots render alongside Auto Ads, not instead of it; if you only want Auto Ads, leave every slot blank.</p></td>
          </tr>
        </table>

        <?php
        // Grouped by the page the slot appears on, in the order the groups
        // are first mentioned in nvl_ad_positions().
        $grouped = array();
        foreach ( nvl_ad_positions() as $position => $meta ) {
            $grouped[ $meta['group'] ][ $position ] = $meta;
        }

        $format_notes = array(
            'auto'     => 'Create this in AdSense as a <strong>Display</strong> unit.',
            'fluid'    => 'Create this in AdSense as an <strong>In-feed</strong> unit — it sits between story cards.',
            'vertical' => 'Create this in AdSense as a <strong>Display</strong> unit; it renders at a fixed 300&times;600.',
        );
        ?>

        <?php foreach ( $grouped as $group => $positions ) : ?>
          <h3><?php echo esc_html( $group ); ?> ad slots</h3>
          <table class="form-table">
            <?php foreach ( $positions as $position => $meta ) :
                $option = "nvl_adsense_slot_{$position}"; ?>
              <tr>
                <th><label for="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $meta['label'] ); ?></label></th>
                <td>
                  <input type="text" id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr( get_option( $option ) ); ?>" placeholder="1234567890" class="regular-text">
                  <p class="description"><?php echo wp_kses( $format_notes[ $meta['format'] ], array( 'strong' => array() ) ); ?></p>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endforeach; ?>

        <h2>Social preview fallback image</h2>
        <table class="form-table">
          <tr>
            <th><label for="nvl_default_share_image">Default share image URL</label></th>
            <td>
              <input type="url" id="nvl_default_share_image" name="nvl_default_share_image" value="<?php echo esc_attr( get_option( 'nvl_default_share_image' ) ); ?>" placeholder="https://your-domain.com/wp-content/uploads/default-share.jpg" class="regular-text">
              <p class="description">Used only on the rare story/chapter published without a Featured Image, so social shares never go out with no thumbnail. Recommended size 1200&times;630.</p>
            </td>
          </tr>
        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}

/**
 * Nudge editors in the post list + editor screen if a story/chapter was
 * published without a Featured Image, since that image is what social
 * platforms use for the link preview thumbnail.
 */
function nvl_missing_thumbnail_notice() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'nvl_story' ) return;

    global $post;
    if ( $post && $post->post_status === 'publish' && ! has_post_thumbnail( $post->ID ) ) {
        echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'No Featured Image set.', 'noirvella-stories' ) . '</strong> ' . esc_html__( 'This post is published without one, so social shares (Facebook, X, etc.) will fall back to the default share image, or show no thumbnail if none is set in Settings -> Noirvella.', 'noirvella-stories' ) . '</p></div>';
    }
}
add_action( 'edit_form_top', 'nvl_missing_thumbnail_notice' );

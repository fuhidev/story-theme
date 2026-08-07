<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * GA4 -- only outputs if an ID is set in Settings -> Noirvella. Loaded with
 * async, and placed in wp_head at low priority so it never delays the
 * cover image / prose (the actual content that matters for the reader and
 * for LCP).
 */
function nvl_output_ga4() {
    $id = trim( get_option( 'nvl_ga4_id' ) );
    if ( ! $id || is_admin() ) return;
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js( $id ); ?>');
    </script>
    <?php
}
add_action( 'wp_head', 'nvl_output_ga4', 90 );

/**
 * AdSense Auto Ads -- only outputs if a Publisher ID is set. This alone is
 * enough for AdSense to start placing ads automatically; the manual ad
 * slots below are optional, for when specific in-article placements are
 * wanted instead of / alongside Auto Ads.
 */
function nvl_output_adsense_script() {
    $client = trim( get_option( 'nvl_adsense_client' ) );
    if ( ! $client || is_admin() ) return;
    ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo esc_attr( $client ); ?>" crossorigin="anonymous"></script>
    <?php
}
add_action( 'wp_head', 'nvl_output_adsense_script', 91 );

/**
 * Every manual ad placement the theme offers, in one registry. Both the
 * settings screen and the templates read from here, so adding a placement
 * is a single edit rather than "add a field, then remember to add the
 * matching setting, then remember to register it".
 *
 * 'format' maps to the AdSense unit type the position is shaped for:
 *   auto     -- responsive display unit (most positions)
 *   fluid    -- in-feed unit, sits between story cards in a grid
 *   vertical -- tall 300x600 skyscraper for the sticky chapter sidebar
 *
 * 'group' only controls how the settings screen is organised.
 */
function nvl_ad_positions() {
    return array(
        'header'       => array( 'group' => 'Global',   'format' => 'auto',     'label' => 'Under the site header (all pages)' ),
        'home_top'     => array( 'group' => 'Homepage', 'format' => 'auto',     'label' => 'Homepage — under the hero' ),
        'home_mid'     => array( 'group' => 'Homepage', 'format' => 'auto',     'label' => 'Homepage — between Latest updates and Most read' ),
        'home_bottom'  => array( 'group' => 'Homepage', 'format' => 'auto',     'label' => 'Homepage — above the footer' ),
        'infeed'       => array( 'group' => 'Homepage', 'format' => 'fluid',    'label' => 'In-feed — inside the story grid, after the 6th card' ),
        'story_top'    => array( 'group' => 'Story',    'format' => 'auto',     'label' => 'Story page — under the description' ),
        'story_bottom' => array( 'group' => 'Story',    'format' => 'auto',     'label' => 'Story page — under the chapter list' ),
        'top'          => array( 'group' => 'Chapter',  'format' => 'auto',     'label' => 'Chapter — after the cover image' ),
        'mid'          => array( 'group' => 'Chapter',  'format' => 'auto',     'label' => 'Chapter — inside the text, about a third of the way down' ),
        'bottom'       => array( 'group' => 'Chapter',  'format' => 'auto',     'label' => 'Chapter — before the share buttons' ),
        'sidebar'      => array( 'group' => 'Chapter',  'format' => 'vertical', 'label' => 'Chapter — sticky sidebar, under the table of contents' ),
        'archive'      => array( 'group' => 'Archive',  'format' => 'auto',     'label' => 'Archive / genre / search — above the grid' ),
    );
}

/**
 * Manual ad unit renderer used by the theme templates. Call
 * nvl_render_ad_slot( 'top' ), nvl_render_ad_slot( 'infeed' ), etc.
 * Renders nothing at all if the publisher ID or that slot's ID is unset,
 * so a half-configured install shows no empty boxes.
 *
 * The "Advertisement" label is not decoration: AdSense policy requires ad
 * units to be distinguishable from surrounding content, which matters most
 * for the in-feed slot sitting between real story cards and for the
 * in-article slot sitting inside the prose.
 */
function nvl_render_ad_slot( $position, $args = array() ) {
    $client = trim( (string) get_option( 'nvl_adsense_client' ) );
    $slot   = trim( (string) get_option( "nvl_adsense_slot_{$position}" ) );
    if ( ! $client || ! $slot ) return;

    $positions = nvl_ad_positions();
    $format    = isset( $args['format'] )
        ? $args['format']
        : ( isset( $positions[ $position ]['format'] ) ? $positions[ $position ]['format'] : 'auto' );

    $classes = 'ad-slot ad-slot-' . $position . ' ad-format-' . $format;
    if ( ! empty( $args['class'] ) ) $classes .= ' ' . $args['class'];
    ?>
    <div class="<?php echo esc_attr( $classes ); ?>">
      <span class="ad-label"><?php esc_html_e( 'Advertisement', 'noirvella-stories' ); ?></span>
      <?php if ( $format === 'fluid' ) : ?>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="<?php echo esc_attr( $client ); ?>"
             data-ad-slot="<?php echo esc_attr( $slot ); ?>"
             data-ad-format="fluid"
             data-ad-layout="in-article"></ins>
      <?php elseif ( $format === 'vertical' ) : ?>
        <ins class="adsbygoogle"
             style="display:inline-block;width:300px;height:600px"
             data-ad-client="<?php echo esc_attr( $client ); ?>"
             data-ad-slot="<?php echo esc_attr( $slot ); ?>"></ins>
      <?php else : ?>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="<?php echo esc_attr( $client ); ?>"
             data-ad-slot="<?php echo esc_attr( $slot ); ?>"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
      <?php endif; ?>
      <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
    <?php
}

/**
 * True when a slot would actually render. Templates use this to decide
 * whether to emit a wrapper (a grid cell, a sidebar block) around the ad,
 * so an unconfigured slot doesn't leave an empty box in the layout.
 */
function nvl_has_ad_slot( $position ) {
    return (bool) ( trim( (string) get_option( 'nvl_adsense_client' ) ) && trim( (string) get_option( "nvl_adsense_slot_{$position}" ) ) );
}

/**
 * Serves /ads.txt straight from WordPress using the saved Publisher ID, so
 * there's one less manual file to keep in sync. If a physical ads.txt
 * already exists on the server, Nginx will serve that static file first
 * and this never runs -- which is fine, either path works.
 */
function nvl_maybe_serve_ads_txt() {
    if ( trim( $_SERVER['REQUEST_URI'] ?? '', '/' ) !== 'ads.txt' ) return;

    $client = trim( get_option( 'nvl_adsense_client' ) );
    if ( ! $client ) return;

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "google.com, {$client}, DIRECT, f08c47fec0942fa0\n";
    exit;
}
add_action( 'template_redirect', 'nvl_maybe_serve_ads_txt', 1 );

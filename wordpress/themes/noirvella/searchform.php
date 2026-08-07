<?php
/**
 * Search form. Used in the header and on the results page. Carries
 * post_type so the query is already scoped before pre_get_posts sees it.
 */
$nvl_search_id = 'nvl-search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <label class="screen-reader-text" for="<?php echo esc_attr( $nvl_search_id ); ?>"><?php esc_html_e( 'Search stories', 'noirvella' ); ?></label>
  <input type="search" id="<?php echo esc_attr( $nvl_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search stories…', 'noirvella' ); ?>" autocomplete="off">
  <input type="hidden" name="post_type" value="nvl_story">
  <button type="submit" aria-label="<?php esc_attr_e( 'Search', 'noirvella' ); ?>">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <circle cx="9" cy="9" r="5.5" stroke="currentColor" stroke-width="1.6"/>
      <path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
    </svg>
  </button>
</form>

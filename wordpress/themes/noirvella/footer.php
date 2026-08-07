</div><!-- #nvl-content -->

<footer class="site-footer">
  <span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
  <nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'noirvella' ); ?>">
    <a href="<?php echo esc_url( get_post_type_archive_link( 'nvl_story' ) ); ?>"><?php esc_html_e( 'All stories', 'noirvella' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>"><?php esc_html_e( 'Contact us', 'noirvella' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/terms-and-conditions' ) ); ?>"><?php esc_html_e( 'Terms and conditions', 'noirvella' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'noirvella' ); ?></a>
  </nav>
  <span><?php esc_html_e( 'Serialized drama, one chapter at a time.', 'noirvella' ); ?></span>
</footer>

<?php wp_footer(); ?>
</body>
</html>

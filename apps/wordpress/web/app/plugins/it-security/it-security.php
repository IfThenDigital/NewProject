<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Security - IfThen []
 * Description:     Provides some baseline security changes for a Wordpress application.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-security
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\Security\Service\WordpressSecurityService;

class IfThenSecurity {

  public function __construct() {

    add_action( 'init', function() {
      // Disable all out of the box Wordpress REST endpoints.
      WordpressSecurityService::disable_default_wordpress_rest_endpoints();

      // Add response headers to prevent click jacking.
      // Disabling for now due to an issue with how Gravity Forms handles AJAX forms.
      // WordpressSecurityService::prevent_click_jacking();
    });

  }

}

new IfThenSecurity();

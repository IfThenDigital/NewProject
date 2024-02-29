<?php
/**
 * @wordpress-plugin
 * Plugin Name:     SEO Functionality - IfThen []
 * Description:     Any SEO related functionality.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-core
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\SEO\Service\WordpressSEOService;
use IfThen\SEO\Service\YoastSEOService;

class IfThenSEO {

  public function __construct() {

    add_action( 'init', function() {
      // Set the robots.txt file to a default.
      WordpressSEOService::set_robots_txt();

      // Ensure the robots meta tag is removed.
	    WordpressSEOService::disable_robots_meta_tag();

      // Disable the Yoast SEO robots meta tag.
      YoastSEOService::disable_yoast_robots_meta();

      YoastSEOService::disable_yoast_admin_columns_caching();
    }, 100 );

  }

}

new IfThenSEO();

<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Pantheon Tools - IfThen []
 * Description:     Provides tools and utility for the Pantheon platform.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-pantheon
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\Pantheon\Cache\RestAPICache;
use IfThen\Pantheon\Commands\PantheonCacheCommand;

defined('ABSPATH') or exit;

// Plugin classes are autoloaded via composer at the root of the application.
require_once plugin_dir_path(__FILE__) . '../../../../vendor/autoload.php';

class IfThenPantheon {

  public function __construct() {
    RestAPICache::disable_rest_caching();

    add_action( 'init', function() {
      // Only execute for WP_CLI.
      if (defined( 'WP_CLI' ) && WP_CLI) {
        new PantheonCacheCommand();
      }
    });

  }

}

new IfThenPantheon();
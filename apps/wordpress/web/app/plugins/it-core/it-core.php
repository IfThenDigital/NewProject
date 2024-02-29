<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Core Functionality - IfThen []
 * Description:     Functionality used by other IfThen plugins.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-core
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\Core\ServiceProvider;
use IfThen\Core\Schema\PostType\AlterPage;
use IfThen\Core\Schema\PostType\AlterPost;
use IfThen\Utility\Wordpress\CacheUtility;
use IfThen\Utility\Wordpress\PostUtility;
use IfThen\Utility\Wordpress\Shortcode\CurrentYearShortcode;
use IfThen\Utility\WPCFM\Commands\WPCFMCommand;
use IfThen\Utility\WPCFM\WPCFMSettings;

defined('ABSPATH') or exit;

// Plugin classes are autoloaded via composer at the root of the application.
require_once plugin_dir_path(__FILE__) . '../../../../vendor/autoload.php';

class IfThenCore {

  public function __construct() {
    // Initialize the routing system.
    Brain\Cortex::boot();

    add_action( 'init', function() {
      // Initialize our service container wrapper.
      ServiceProvider::init();

      // Default changes for pages and posts.
    	new AlterPage();
    	new AlterPost();

      // Caching related changes.
      CacheUtility::cacheBustImageOnCrop();

      // Alter core Rest endpoints.
      PostUtility::alterPageRestEndpoint();

      // Custom shortcodes.
      new CurrentYearShortcode();

      // Any custom WP-CLI commands.
      new WPCFMCommand();
    });

    // Enable any WP-CFM settings changes. Notably, ensuring the config directory is correctly set.
    new WPCFMSettings();

  }

}

new IfThenCore();

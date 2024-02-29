<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Advanced Custom Fields Helper - IfThen []
 * Description:     Custom functionality for use with advanced custom fields plugin.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-acf
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\Acf\Commands\AcfHelperCommand;

defined('ABSPATH') or exit;

// Plugin classes are autoloaded via composer at the root of the application.
require_once plugin_dir_path(__FILE__) . '../../../../vendor/autoload.php';

class IfThenAcf {

  public function __construct() {

    add_action( 'init', function() {
      // Only execute for WP_CLI.
      if (defined( 'WP_CLI' ) && WP_CLI) {
        new AcfHelperCommand();
      }
    } );


    // Filter callbacks.
    add_filter('acf/settings/save_json', array( $this, 'alterAcfSaveJsonPath' ));
    add_filter('acf/settings/load_json', array( $this, 'alterAcfLoadJsonPath' ));

  }

  public function alterAcfSaveJsonPath() {
    return plugin_dir_path(__FILE__) . '../../config/acf';
  }

  public function alterAcfLoadJsonPath() {
    return [ plugin_dir_path(__FILE__) . '../../config/acf' ];
  }

}

new IfThenAcf();

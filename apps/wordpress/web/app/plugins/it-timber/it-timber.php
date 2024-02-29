<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Timber Helper - IfThen []
 * Description:     Custom functionality for use the Timber library.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-timber
 * Domain Path:     /languages
 * Version:         1.0.0
 */

use IfThen\Timber\Twig\TwigFilters;
use IfThen\Timber\Twig\TwigFunctions;

defined('ABSPATH') or exit;

// Plugin classes are autoloaded via composer at the root of the application.
require_once plugin_dir_path(__FILE__) . '../../../../vendor/autoload.php';

class IfThenTimber {

  public function __construct() {

    add_action( 'init', function() {
      new TwigFilters();
      new TwigFunctions();
    });

  }

}

new IfThenTimber();
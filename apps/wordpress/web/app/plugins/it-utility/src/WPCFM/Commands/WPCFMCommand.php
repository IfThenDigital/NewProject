<?php


namespace IfThen\Utility\WPCFM\Commands;

use WP_CLI;

/**
 * Provides additional WP_CLI commands for working with WPCFM bundles.
 *
 * @package IfThen\Utility\WPCFM\Commands
 */
class WPCFMCommand {

  public function __construct() {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      WP_CLI::add_command( 'it:config', self::class );
    }
  }

  /**
   * Deletes a given bundle from the database settings.
   *
   * @param $args
   */
  public function delete($args) {
    $bundle_name = $args[0];
    // Grab the bundles from the settings in the database.
    $settings = json_decode(WPCFM()->options->get('wpcfm_settings'), true);

    WP_CLI::line("Searching database for bundle {$bundle_name}.");

    // Loop through and find the bundle we want to delete.
    $bundle_found = false;
    if (!empty($settings['bundles'])) {
      foreach ($settings['bundles'] as $key => $bundle) {
        if ($bundle['name'] == $bundle_name) {
          // Bundle found. Remove it from the settings array.
          unset($settings['bundles'][$key]);
          $bundle_found = true;

          break;
        }
      }
    }

    if ($bundle_found) {
      WP_CLI::line("Bundle found. Deleting from database.");

      if (WPCFM()->options->update('wpcfm_settings', json_encode( $settings ))) {
        WP_CLI::success("Bundle {$bundle_name} deleted from database!");
      }
      else {
        WP_CLI::warning("Unable to delete {$bundle_name} from database.");
      }
    }
    else {
      WP_CLI::line("Bundle {$bundle_name} not found in database.");
    }
  }

}
<?php

/**
 * Class NewSiteCommands.
 *
 * Provides a WP CLI command to initialize a new site. Essentially it installs WP-CFM
 * and then imports any configuration bundles.
 */
class NewSiteCommands {

  public function __construct() {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      WP_CLI::add_command( 'it:site', self::class );
    }
  }

  public function init() {
    WP_CLI::line('Activating WP-CFM plugin.');

    // Activate WP-CFM.
    WP_CLI::runcommand('plugin activate wp-cfm');

    WP_CLI::line('Importing all WP-CFM configuration bundles.');

    // Run the WP CLI command to pull all configuration for WP-CFM.
    WP_CLI::runcommand('config pull all');

    WP_CLI::success('Your Wordpress site is initialized!');
  }

}

function init_new_site_commands() {
  $new_site_commands = new NewSiteCommands();
}

init_new_site_commands();
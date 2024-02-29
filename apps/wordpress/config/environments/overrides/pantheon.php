<?php
/**
 * Configuration overrides for WP_ENV === 'pantheon'.
 */

use Roots\WPConfig\Config;

// ** MySQL settings - included in the Pantheon Environment ** //
/** The name of the database for WordPress */
Config::define('DB_NAME', $_ENV['DB_NAME']);

/** MySQL database username */
Config::define('DB_USER', $_ENV['DB_USER']);

/** MySQL database password */
Config::define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

/** MySQL hostname; on Pantheon this includes a specific port number. */
Config::define('DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']);

/** Database Charset to use in creating database tables. */
Config::define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
Config::define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * Pantheon sets these values for you also. If you want to shuffle them you
 * must contact support: https://pantheon.io/docs/getting-support
 *
 * @since 2.6.0
 */
Config::define('AUTH_KEY',         $_ENV['AUTH_KEY']);
Config::define('SECURE_AUTH_KEY',  $_ENV['SECURE_AUTH_KEY']);
Config::define('LOGGED_IN_KEY',    $_ENV['LOGGED_IN_KEY']);
Config::define('NONCE_KEY',        $_ENV['NONCE_KEY']);
Config::define('AUTH_SALT',        $_ENV['AUTH_SALT']);
Config::define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
Config::define('LOGGED_IN_SALT',   $_ENV['LOGGED_IN_SALT']);
Config::define('NONCE_SALT',       $_ENV['NONCE_SALT']);
/**#@-*/

/** A couple extra tweaks to help things run well on Pantheon. **/
if (isset($_SERVER['HTTP_HOST'])) {
  // HTTP is still the default scheme for now. 
  $scheme = 'http';
  // If we have detected that the end use is HTTPS, make sure we pass that
  // through here, so <img> tags and the like don't generate mixed-mode
  // content warnings.
  if (isset($_SERVER['HTTP_USER_AGENT_HTTPS']) && $_SERVER['HTTP_USER_AGENT_HTTPS'] == 'ON') {
    $scheme = 'https';
    $_SERVER['HTTPS'] = 'on';
  }
  Config::define('WP_HOME', $scheme . '://' . $_SERVER['HTTP_HOST']);
  Config::define('WP_SITEURL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wp');
  Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));
}
// Don't show deprecations; useful under PHP 5.5
error_reporting(E_ALL ^ E_DEPRECATED);
/** Config::define appropriate location for default tmp directory on Pantheon */
Config::define('WP_TEMP_DIR', $_SERVER['HOME'] .'/tmp');

// FS writes aren't permitted in test or live, so we should let WordPress know to disable relevant UI
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'test', 'live' ) ) && ! Config::get( 'DISALLOW_FILE_MODS' ) ) :
  Config::define( 'DISALLOW_FILE_MODS', true );
endif;
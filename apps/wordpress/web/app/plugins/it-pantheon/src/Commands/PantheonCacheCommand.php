<?php

namespace IfThen\Pantheon\Commands;

use Pantheon_Cache;
use WP_CLI;

/**
 * Class PantheonCacheCommand.
 *
 * @package IfThen\Pantheon\Commands
 */
class PantheonCacheCommand {

  public function __construct() {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      WP_CLI::add_command( 'it:pantheon:cache', self::class );
    }
  }

  /**
   *
   */
  public function clear() {
    Pantheon_Cache::instance()->flush_site();
  }

}

<?php


namespace IfThen\Utility\Wordpress;


class AutoUpdaterUtility {

  public static function auto_update_core() {
    add_filter( 'auto_update_core', '__return_true' );
  }

  public static function auto_update_plugins() {
    add_filter( 'auto_update_plugin', '__return_true' );
  }

}
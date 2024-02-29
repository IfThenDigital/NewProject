<?php


namespace IfThen\Utility\Utility;


class Logger {

  public static function log($log) : void {

    if (is_array($log) || is_object($log)) {
      error_log(print_r($log, TRUE));
    }
    else {
      error_log($log);
    }

  }

}
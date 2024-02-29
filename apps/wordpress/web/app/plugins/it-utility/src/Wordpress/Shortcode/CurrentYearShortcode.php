<?php

namespace IfThen\Utility\Wordpress\Shortcode;

class CurrentYearShortcode {

  public function __construct() {
    add_shortcode('current-year', array( $this, 'get_shortcode' ) );
  }

  public function get_shortcode() {
    return date('Y');
  }

}

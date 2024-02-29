<?php


namespace IfThen\Pantheon\Cache;


class RestAPICache {

  public static function disable_rest_caching() {
    // wp-json paths or any custom endpoints
    $regex_json_path_patterns = array(
      '#^/wp-json/wp/v2?#',
      '#^/wp-json/?#'
    );

    foreach ($regex_json_path_patterns as $regex_json_path_pattern) {
      if (preg_match($regex_json_path_pattern, $_SERVER['REQUEST_URI'])) {
        // Re-use the rest_post_dispatch filter in the Pantheon page cache plugin
        add_filter( 'rest_post_dispatch', function( $response, $server ) {
          $server->send_header( 'Cache-Control', 'no-cache, must-revalidate, max-age=0' );
          return $response;
        }, 12, 2 );
      }
    }
  }

}
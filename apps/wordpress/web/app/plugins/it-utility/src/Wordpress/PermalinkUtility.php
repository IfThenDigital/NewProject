<?php


namespace IfThen\Utility\Wordpress;


class PermalinkUtility {

  /**
   * Cleans a Wordpress permalink.
   *
   * @param $permalink
   *
   * @return string
   */
  public static function clean_permalink( $permalink ) {
    // Handle permalinks within a Pantheon environment.
    if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
      // Sometimes permalinks will have 'pantheon' set as their domain, rather than the
      // actual 'https://domain.com'. In this instance we just remove the pantheon
      // and the permalink will be treated as relative to the root domain of the site.
      $permalink = preg_replace( '/^pantheon/', '', $permalink, 1 );
    }

    return $permalink;
  }

}

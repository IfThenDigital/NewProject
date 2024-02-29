<?php


namespace IfThen\SEO\Service;


class WordpressSEOService {

	public static function disable_robots_meta_tag() {

		add_filter( 'wp_robots', function( $robots ) {
			return array();
		});
	}

  public static function set_robots_txt() {

    add_filter( 'robots_txt', function( $output, $public = false ) {
      $output = <<<ROBOTS
  User-agent: *
  Disallow: /wp-admin/
  Disallow: /trackback/
  Disallow: /xmlrpc.php
  Disallow: /feed/
  Allow: /wp-admin/admin-ajax.php
ROBOTS;

      return $output;
    } );

  }
}
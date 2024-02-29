<?php

namespace IfThen\Security\Service;

class WordpressSecurityService {

  public static function prevent_click_jacking() {
    add_filter('wp_headers', function( $headers ) {
      $headers['X-Frame-Options'] = 'SAMEORIGIN';
      $headers['Content-Security-Policy'] = "frame-ancestors 'none'";

      return $headers;
    });
  }

  public static function disable_default_wordpress_rest_endpoints() {
    add_filter('rest_endpoints', function( $endpoints ) {

      if (!is_user_logged_in()) {
        foreach( $endpoints as $route => $endpoint ){
          if( 0 === stripos( $route, '/wp/' ) ){
            unset( $endpoints[ $route ] );
          }
        }
      }

      return $endpoints;
    });
  }

  public static function disable_comments_rest_endpoint() {
    add_filter('rest_endpoints', function( $endpoints ) {

      if (!is_user_logged_in()) {
        foreach( $endpoints as $route => $endpoint ){
          if( 0 === stripos( $route, '/wp/v2/comments' ) ){
            unset( $endpoints[ $route ] );
          }
        }
      }

      return $endpoints;
    });
  }

  public static function disable_users_rest_endpoint() {
    add_filter('rest_endpoints', function( $endpoints ) {

      if (!is_user_logged_in()) {
        foreach ($endpoints as $route => $endpoint) {
          if (0 === stripos($route, '/wp/v2/users')) {
            unset($endpoints[$route]);
          }
        }
      }

      return $endpoints;
    });
  }

  public static function disable_oembed_rest_endpoint() {
    add_filter('rest_endpoints', function( $endpoints ) {

      if (!is_user_logged_in()) {
        foreach ($endpoints as $route => $endpoint) {
          if (0 === stripos($route, '/oembed')) {
            unset($endpoints[$route]);
          }
        }
      }

      return $endpoints;
    });
  }

}
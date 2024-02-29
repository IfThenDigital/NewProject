<?php


namespace IfThen\Timber\Twig;

use IfThen\Utility\Utility\Handy;
use Timber\Timber;
use Timber\Twig_Function;
use Timber\URLHelper;
use Twig\Environment;
use WP;

class TwigFunctions {

  public function __construct() {
    add_filter('timber/twig', array( $this, 'add_twig_functions' ) );
  }

  public function add_twig_functions(Environment $twig) {
    $twig->addFunction(new Twig_Function('it_get_current_url', function() {
      return URLHelper::get_current_url();
    }));

    $twig->addFunction(new Twig_Function('it_get_url_querystring', function() {
      /* @var WP $wp */
      global $wp;

      return $wp->query_string;
    }));

    $twig->addFunction(new Twig_Function('it_link_href', function( $link ) {
      $link_attributes = "href=\"\"";

      if (isset($link['url'])) {
        $link_attributes = "href=\"{$link['url']}\"";
      }

      if (isset($link['target']) && $link['target']) {
        $link_attributes .= " target=\"{$link['target']}\" rel=\"noopener noreferrer\"";
      }

      return $link_attributes;
    }));

    $twig->addFunction(new Twig_Function('it_number_to_roman', function($number) {
      return Handy::numberToRoman($number);
    }));

    return $twig;
  }

  public function getTimberContext() {
    return call_user_func('Timber::context');
  }
}

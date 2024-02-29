<?php

namespace IfThen\Timber\Twig;

use IfThen\Utility\Utility\Handy;
use Timber\Twig_Filter;
use Twig\Environment;

/**
 * Class TwigFilters.
 *
 * Custom twig filters.
 *
 * @package IfThen\Utility\Twig
 */
class TwigFilters {

  public function __construct() {
    add_filter('timber/twig', array( $this, 'add_twig_filters' ) );
  }

  public function add_twig_filters( Environment $twig ) {
    $twig->addFilter(new Twig_Filter('it_clean_phone', [$this, 'cleanPhoneNumber']));
    $twig->addFilter(new Twig_Filter('it_generate_block_id', [$this, 'generateBlockId']));
    $twig->addFilter(new Twig_Filter('it_clean_alt', [$this, 'escapeAltText']));
    $twig->addFilter(new Twig_Filter('it_html_entity_decode', [$this, 'htmlEntityDecode']));
    $twig->addFilter(new Twig_Filter('it_json_encode', [$this, 'jsonEncode']));

    return $twig;
  }

  /**
   * Cleans a phone number of all non-digit values.
   *
   * @param $phoneNumber
   *   The phone number value.
   *
   * @return string
   */
  public function cleanPhoneNumber($phoneNumber) {
    return preg_replace('/\D+/', '', $phoneNumber);
  }

  /**
   * Used to generate unique block ids for preview mode in Gutenberg editor.
   *
   * @param $elementId
   *   The element id to prepend to the timestamp.
   *
   * @return string
   *   Block id.
   */
  public function generateBlockId($elementId) {
    return $elementId . time();
  }

  public function escapeAltText($altText) {
    return htmlentities($altText);
  }

  public function htmlEntityDecode($html) {
    return html_entity_decode($html, ENT_QUOTES|ENT_XML1, 'UTF-8');
  }

  public function jsonEncode($data) {
    return Handy::jsonEncode( $data );
  }

}

<?php

namespace IfThen\Utility\Wordpress;

use WP_Term;

/**
 * Class TaxonomyUtility.
 *
 * Provides supporting functionality for getting term data.
 *
 * @package IfThen\Utility\Wordpress
 */
class TaxonomyUtility {

  /**
   * Returns an array of term names from an array of WP_Term objects.
   *
   * @param WP_Term[] $terms
   *   Terms to get the names from.
   *
   * @return array
   *   An array of term names.
   */
  public static function getTermNames(array $terms) {
    $names = [];

    foreach ($terms as $term) {
      $values[] = $term->name;
    }

    return $names;
  }

  /**
   * Returns an array of term ids from an array of WP_Term objects.
   *
   * @param WP_Term[] $terms
   *   Terms to get the ids from.
   *
   * @return array
   *   An array of term ids.
   */
  public static function getTermIds(array $terms) {
    $ids = [];

    foreach ($terms as $term) {
      $ids[] = $term->term_id;
    }

    return $ids;
  }

}

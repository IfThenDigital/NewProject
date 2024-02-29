<?php

namespace IfThen\Core\Schema\PostType;

/**
 * Class AlterPage.
 *
 * Alters the Page post type.
 *
 * @package Ahc\Core\Schema\PostType
 */
class AlterPage {

  public function __construct() {
    // Filters.
    add_filter('acf/update_value/name=page_featured_image', array( $this, 'handle_featured_image_update' ), 10, 3);
  }

  /**
   * Handles when the featured image ACF field is updated, and updates the
   * Wordpress thumbnail/featured image field with the same image.
   *
   * @param $value
   *   The image id.
   * @param $post_id
   *   The post id.
   * @param $field
   *   The array of ACF field data.
   *
   * @return mixed
   */
  public function handle_featured_image_update($value, $post_id, $field) {
    if ($value) {
      update_post_meta($post_id, '_thumbnail_id', $value);
    }

    return $value;
  }

}
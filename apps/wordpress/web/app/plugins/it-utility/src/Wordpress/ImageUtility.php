<?php


namespace IfThen\Utility\Wordpress;


class ImageUtility {

  public static function deleteImageSize($image_id, $size_name) {
    // A little convoluted, but here we go.
    // First, get the full path of the original image.
    $original_image_path = get_attached_file($image_id);

    // Next, get the meta data for the image.
    $image_meta_data = wp_get_attachment_metadata($image_id, true);

    // Only continue if the meta data contains the size we're looking for.
    if (array_key_exists($size_name, $image_meta_data['sizes'])) {
      // The image size does exist. But we are not given a complete path to the
      // file. So, we must construct one.
      $original_path_info = pathinfo($original_image_path);
      $image_size_info = $image_meta_data['sizes'][$size_name];

      // Construct the full path to the image size file.
      $image_size_file_path = "{$original_path_info['dirname']}/{$image_size_info['file']}";

      // Delete it!
      wp_delete_file($image_size_file_path);
    }
  }

}
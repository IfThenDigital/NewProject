<?php

namespace IfThen\Utility\Wordpress;

class CacheUtility {

  public static function cacheBustImageOnCrop() {
    add_filter('crop_thumbnails_filename', function($destfilename, $file, $w, $h, $crop, $info, $imageMetadata) {
      if ($crop) {
        // Delete the current cropped image.
        // While we don't have enough information to go on from the filter variables.
        // Fortunately, since we know this is happening during a thumbnail crop, the current request
        // has the information we need.
        $crop_thumbnail_request = json_decode(stripcslashes($_REQUEST['crop_thumbnails']), true);

        // Issue an image deletion for each active image size (sizes being cropped).
        foreach ($crop_thumbnail_request['activeImageSizes'] as $image_size) {
          // It's important to note here that if there is more than one image size, this will be call once
          // for each image size. So, on images after the first, there will be no image to delete, as the originals
          // are already deleted.
          // Not a huge concern, but noted.
          ImageUtility::deleteImageSize($crop_thumbnail_request['sourceImageId'], $image_size['name']);
        }


        // Build the new destination file name.
        $cache_buster = time();
        $destfilename = "{$info['dirname']}/{$info['basename']}-{$w}x{$h}-{$cache_buster}.{$info['extension']}";
      }

      return $destfilename;
    },
    10,
    7);
  }

}

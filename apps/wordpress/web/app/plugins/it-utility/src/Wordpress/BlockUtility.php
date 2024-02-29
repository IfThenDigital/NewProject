<?php

namespace IfThen\Utility\Wordpress;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Timber\Post;

class BlockUtility {

  public static function getBlockFields($block_id, $post_id) {
    // Initial values.
    $block_data = null;
    $block_fields = false;

    // Load the post and parse the blocks from the post content.
    $post = new Post($post_id);
    $blocks = parse_blocks($post->post_content);

    if ($blocks) {
      $iterator  = new RecursiveArrayIterator($blocks);
      $recursive = new RecursiveIteratorIterator(
        $iterator,
        RecursiveIteratorIterator::SELF_FIRST
      );
      foreach ( $recursive as $key => $value ) {
        if ( isset($value['attrs']) && isset($value['attrs']['id']) ){
          // Loop through the blocks recursively, looking for a match.
          if ( $value['attrs']['id'] === $block_id ) {
            // We have a match. Change context of "the loop" to be the block.
            acf_setup_meta( $value['attrs']['data'], $value['attrs']['id'], true );

            // With the loop context changed, we can now call get_fields() and get nicely formatted data.
            $block_fields = get_fields();

            // All done. Reset context.
            acf_reset_meta( $value['attrs']['id'] );

            break;
          }
        }
      }
    }

    return $block_fields;
  }

}

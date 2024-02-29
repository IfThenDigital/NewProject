<?php

namespace IfThen\Timber\Service;

use Timber\Post;

/**
 * Class TemplateResolver.
 *
 * Provides a single service to manage resolving Twig template paths.
 *
 * @package IfThen\Timber\Service
 */
class TemplateResolver {

  public static function resolve_post( Post $post ) {
    // Check for cases that would result in a 404 page displayed.
    if ( $post->ID == null ) {
      return array(
        "404.twig",
      );
    }

    // Determine the template name based on the post type.
    if( is_front_page() ) {
      $default_template_file = "post-type/{$post->post_type}.twig";
      $dynamic_template_file = "post-type/home.twig";
    }
    // Podcasts and radio shows use the same template
    elseif ($post->post_type == 'podcast') {
      $default_template_file = "post-type/radio-show.twig";
      $dynamic_template_file = "post-type/radio-show/radio-show-{$post->id}.twig";
    }
    else {
      $default_template_file = "post-type/{$post->post_type}.twig";
      $dynamic_template_file = "post-type/{$post->post_type}/{$post->post_type}-{$post->id}.twig";
    }

    // Return an array of template files.
    return array(
      $dynamic_template_file,
      $default_template_file,
    );
  }

}

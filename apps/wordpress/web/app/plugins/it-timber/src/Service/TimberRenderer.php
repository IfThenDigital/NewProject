<?php

namespace IfThen\Timber\Service;

use Timber\Post;
use Timber\Timber;

class TimberRenderer {

  /**
   * Serves as a wrapper for Timber::render.
   *
   * @param string $template_path
   */
  public static function render( $template_path = '' ) {
    // Get the current context and post.
    $context = Timber::context();

    /* @var Post $context['post'] */
    $context['post'] = Timber::query_post();

    // Determine where the template files will be.
    if ( $template_path != '' ) {
      $template_files = array( $template_path );
    }
    else {
      $template_files = TemplateResolver::resolve_post( $context['post'] );
    }

    // Apply any post type specific filter changes.
    $context = apply_filters( "timber/post/{$context['post']->post_type}", $context );

    // Execute the render.
    Timber::render( $template_files, $context );
  }

  /**
   * Servers as a wrapper around Timber::render for terms.
   *
   * @param string $template_path
   */
  public static function render_term( $template_path = '' ) {
    // Get the current context.
    $context = Timber::context();

    // Retrieve the current WP_Term.
    $current_term = get_term( get_queried_object_id() );

    // Set the page title.
    $context['title'] = $current_term->name;

    // Use any template paths passed in.
    if ( $template_path != '' ) {
      $template_files = array( $template_path );
    }
    else {
      $template_files = array( "taxonomy/{$current_term->taxonomy}.twig" );
    }

    // Pass along the term.
    $context['term'] = $current_term;

    // Apply any term type specific filter changes.
    $context = apply_filters( "timber/taxonomy/{$current_term->taxonomy}", $context );

    Timber::render( $template_files, $context );
  }
}

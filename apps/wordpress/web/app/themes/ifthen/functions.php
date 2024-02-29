<?php
/**
 * IfThen[] Starter Theme
 */

use IfThen\Theme\BaseSite;


/**
 * This inherits from BaseSite, which takes care of a few actions/filters
 * that are common across sites.
 */
class IfThenSite extends BaseSite {

	public function __construct() {
    // Add filters.
    add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'allowed_block_types', array( $this, 'allowed_block_types' ), 10, 2 );

    // Add actions.
    add_action( 'init', array( $this, 'add_image_sizes' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_gutenberg_editor_assets' ) );

    // Enable adding our compiled css as namespaced styles in Gutenberg
    add_theme_support( 'editor-styles' );

    // Call the parent constructor. Important to make sure all of the actions and filters
    // from the base class are added.
		parent::__construct();
	}

  /**
   * Provide additional contextual values to a Twig template before render.
   *
   * @param $context
   *
   * @return mixed
   */
	public function add_to_context( $context ) {
    // Any data added to the context here will be available to all templates.
    $context['is_home'] = is_home();


    // Get the last modified time for CSS and JS. We use this to cache bust every time there is a file change.
    $context['frontend']['cb_css'] = filemtime(get_theme_file_path('/dist/app.css'));
    $context['frontend']['cb_js'] = filemtime(get_theme_file_path('/dist/app.js'));

		return $context;
	}

  /**
   * Adds custom image sizes.
   */
  public function add_image_sizes() {
    // Provide a thumbnail image size for authoring.
    add_image_size('Admin Thumbnail', 250, 250, TRUE);
  }

	/**
	 * Load extra frontend assets while using the Gutenberg editor.
	 */
	public function add_gutenberg_editor_assets() {
		// wp_enqueue_style( 'gutenberg-editor-styles', get_theme_file_uri( '/dist/gutenberg-styles.css' ), false );
		wp_enqueue_script(
			'gutenberg-editor-controls',
			get_theme_file_uri( '/src/services/wordpress/gutenberg-editor-controls.js' ),
			array( 'wp-blocks', 'wp-dom' ),
			time(),
			true
		);
	}

	/**
	 * Alters the list of allowed Gutenberg block types when authoring posts.
	 *
	 * @param $allowed_blocks
	 * @param $post
	 *
	 * @return string[]
	 */
	public function allowed_block_types($allowed_blocks, $post) {

		switch ($post->post_type) {
			case 'post':
			case 'page':
				$allowed_blocks = [
					// Core blocks.
					'core/block',
					'core/paragraph',
					'core/heading',
					'core/list',
					'core/table',
					'core/separator',
					// Third party blocks.
					// Custom blocks.
					'acf/html',
				];

				break;
		}

		return $allowed_blocks;

	}

}

new IfThenSite();

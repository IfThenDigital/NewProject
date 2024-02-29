<?php

namespace IfThen\Acf\Blocks\Block;

use IfThen\Acf\Blocks\Service\BlockManager;
use IfThen\Acf\Blocks\Service\BlockTemplateManager;
use Timber\Timber;

/**
 * Class BaseBlock.
 *
 * Provides base functionality all other blocks can inherit from.
 *
 * @package IfThen\Acf\Blocks\Block
 */
class BaseBlock {

  /**
   * @var array An array of block properties.
   */
  protected $block_definition = array();

  /**
   * Unique identifier for this block instance.
   *
   * @var string
   */
  protected $block_id;

  /**
   * Used as the callback for the ACF Blocks framework when the block is rendered.
   *
   * @param $block
   *   The block being rendered.
   * @param string $content
   *
   * @param bool $is_preview
   *   If the user is viewing a previewed version.
   */
  public function render($block, $content = '', $is_preview = FALSE ) {
    $context = $this->pre_render($block, $is_preview);

    // Render the block.
    Timber::render( $this->get_template_path(), $context );
  }

  public function get_block_definition() {
    return $this->block_definition;
  }

  public function get_block_name() {
    return $this->block_definition['name'];
  }

  public function get_authoring_block_name() {
    return "acf/{$this->block_definition['name']}";
  }

  /**
   * Pre-render is the time to change data structure, or pull data prior to rendering.
   *
   * @param $block
   * @param $is_preview
   *
   * @return array
   */
  protected function pre_render($block, $is_preview ) {
    // Get the Timber context.
    $context = Timber::context();

    // Capture our block id.
    $this->block_id = $block['id'];

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    return $context;
  }

  /**
   * Registers the current block definition via the BlockManager.
   */
  protected function register() {
    if ( !array_key_exists('render_callback', $this->block_definition) || $this->block_definition['render_callback'] == '' ) {
      $this->block_definition['render_callback'] = [$this, 'render'];
    }

    if ( function_exists('acf_register_block_type') ) {
      acf_register_block_type( $this->block_definition );
    }

    // Until render time, when ACF provides us with an instance specific block id,
    // let's use the block name.
    $this->block_id = $this->block_definition['name'];

    // Register this block with the block manager.
    BlockManager::get_instance()->add_block( $this );
  }

  /**
   * Returns the template path for this block.
   *
   * @return string|null
   */
  public function get_template_path() {
    // Check the template manager first to see if a Twig template is defined for this block.
    $path = BlockTemplateManager::get_instance()->getTemplatePath( $this->block_definition['name'] );

    // If no path was found in the templates, provide a default path:
    // `components/{name}/{name}.twig`
    if ($path == NULL) {
      $path = "components/{$this->block_definition['name']}/{$this->block_definition['name']}.twig";
    }

    return $path;
  }

  /**
   * Prevents the wptexturize filter from being run on the content of this block.
   */
  protected function disable_wptexturize() {
    add_filter(
      'render_block',
      function ( $block_content, $block ) {
        if ( "acf/{$this->block_definition['name']}" === $block['blockName'] ) {
          remove_filter( 'acf_the_content', 'wptexturize' );
          remove_filter( 'the_content', 'wptexturize' );
        }

        return $block_content;
      },
      10, 2 );
  }

  /**
   * Caches data for the block.
   *
   * @param $data
   *   The data to cache.
   * @param string $cache_id
   *   The unique cache id to use when setting the cache data.
   * @param int $duration
   *   The duration to cache the data. Defaults to never expire.
   */
  protected function set_cache_data($data, $cache_id, $duration = -1 ) {
    $cache_key = $this->get_cache_key( $cache_id );

    wp_cache_set( $cache_key, $data, '', $duration );
  }

  /**
   * Retrieves cached data based on the cache id.
   *
   * @param $cache_id
   *   The unique cache id to use when retrieving cached data.
   *
   * @return bool|mixed
   *   The cached data if successful, or false if no data to return.
   */
  protected function get_cache_data($cache_id ) {
    $cache_key = $this->get_cache_key( $cache_id );

    return wp_cache_get( $cache_key );
  }

  /**
   * Gets the cache key using the unique cache id. The id is namespaced just to avoid any default
   * block caching collisions.
   *
   * @param $cache_id
   *   The unique cache id.
   *
   * @return string
   *   The cache key.
   */
  protected function get_cache_key($cache_id ) {
    // Namespace the cache key with "acf_block" to avoid cache collisions with default
    // block ids.
    return "acf_block:{$cache_id}";
  }

}

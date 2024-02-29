<?php

namespace IfThen\Acf\Blocks\Service;

use IfThen\Acf\Blocks\Block\BaseBlock;
use IfThen\Core\ServiceContainer;

/**
 * Used for any global management of blocks. Right now only handles limiting block types in Gutenberg.
 *
 * Class BlockManager
 * @package IfThen\Acf\Blocks\Service
 */
class BlockManager {

  protected $block_authoring_names = array();

  /**
   * @var BaseBlock[]
   */
  protected $blocks = array();

  /**
   * @var BlockManager The current instance.
   */
  protected static $instance = NULL;

  /**
   * Returns the current instance of BlockTemplateManager.
   *
   * @return BlockManager
   */
  public static function get_instance() {
    if ( self::$instance == NULL ) {
      self::$instance = new BlockManager();
    }

    return self::$instance;
  }

  public function __construct() {
    ServiceContainer::get_instance()->set('blocks.manager', $this);
  }

  /**
   * Adds a block to the manager.
   *
   * @param BaseBlock $block
   *   The block to add.
   */
  public function add_block( BaseBlock $block ) {
    $this->blocks[] = $block;
    $this->block_authoring_names[] = $block->get_authoring_block_name();
  }

  /**
   * Get a block by it's machine name/slug.
   *
   * @param $block_name
   *   The name of the block.
   *
   * @return BaseBlock|null
   */
  public function get_block( $block_name ) {
    $found_block = NULL;

    foreach ( $this->blocks as $block ) {
      if ( $block->get_block_name() == $block_name ) {
        $found_block = $block;
        break;
      }
    }

    return $found_block;
  }

  /**
   * Removes a block from the manager.
   *
   * @param $block_name
   *   The block name used by Gutenberg.
   */
  public function remove_block_from_authoring( $block_name ) {
    if ( $key = array_search( $block_name, $this->block_authoring_names ) ) {
      unset( $this->block_authoring_names[$key] );
    }
  }

  /**
   * This adds a filter callback to limit the authorable Gutenberg block types.
   */
  public function limit_gutenberg_block_types() {
    add_filter( 'allowed_block_types', array( BlockManager::get_instance(), 'allowBlockTypes' ) );
  }

  /**
   * Returns all registered block names.
   *
   * @return array
   *  All registered block names.
   */
  public function get_block_authoring_names() {
    return $this->block_authoring_names;
  }

  /**
   * Wordpress callback to limit the Gutenberg block types to the blocks registered here.
   *
   * @param $enable_all
   *
   * @param null $post
   *
   * @return array
   */
  public function allow_block_types( $enable_all, $post = NULL ) {
    return $this->get_block_authoring_names();
  }

}

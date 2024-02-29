<?php

namespace IfThen\Acf\Blocks\Service;

use Exception;
use IfThen\Utility\Utility\Logger;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

/**
 * Class BlockTemplateManager.
 *
 * A singleton class responsible for loading and managing the Twig template paths
 * for blocks.
 *
 * @package IfThen\Acf\Blocks\Service
 */
class BlockTemplateManager {

  /**
   * @var BlockTemplateManager The current instance.
   */
  protected static $instance = NULL;

  /**
   * @var array An array of template paths.
   */
  protected $templates;

  /**
   * Returns the current instance of BlockTemplateManager.
   *
   * @return BlockTemplateManager
   */
  public static function get_instance() {
    if (self::$instance == NULL) {
      self::$instance = new BlockTemplateManager();
    }

    return self::$instance;
  }

  /**
   * BlockTemplateManager constructor.
   */
  public function __construct() {
    $this->loadTemplates();
  }

  /**
   * Returns the template path for a given block id.
   *
   * @param $block_id
   *   Id of a block.
   *
   * @return string|null
   *   The path of the template.
   */
  public function getTemplatePath( $block_id ) {
    $template_path = NULL;

    if ( array_key_exists($block_id, $this->templates) ) {
      $template_path = $this->templates[$block_id];
    }

    return $template_path;
  }

  /**
   * Checks if the given block id has a template.
   *
   * @param $block_id
   *   Id of the block to check for.
   *
   * @return bool
   *   True if a template is found, false if not.
   */
  public function hasTemplate( $block_id ) {
    return array_key_exists( $block_id, $this->templates );
  }

  /**
   * Searches the `/templates` directory in the current theme, saving the template paths of all templates marked
   * with a @BlockID annotation.
   */
  protected function loadTemplates() {
    // Initialize the templates array.
    $this->templates = array();

    try {
      $iterator = new RecursiveDirectoryIterator(get_template_directory() . '/templates', \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS);

      // Iterate through all the Twig files in the template directory.
      foreach(new RecursiveIteratorIterator($iterator) as $file) {
        // Make sure we have a TWIG file.
        if ($file->getExtension() == 'twig') {
          $file_meta_data = get_file_data($file->getPathname(), [
            'block_id' => 'BlockID'
          ]);

          if ($file_meta_data['block_id'] != '') {
            // Make sure a template isn't already defined.
            if (!$this->hasTemplate($file_meta_data['block_id'])) {
              $this->templates[$file_meta_data['block_id']] = $file->getPathname();
            }
            else {
              // We have a second template defined for a block.
              // Log a meaningful error message.
              $template_in_use = $this->getTemplatePath($file_meta_data['block_id']);

              error_log("An error occurred while trying to assign the template {$file->getPathname()} to 
            the block {$file_meta_data['block_id']}. Block {$file_meta_data['block_id']} is already 
            using the template $template_in_use.");
            }
          }
        }
      }
    }
    catch (Exception $e) {
      Logger::log( $e->__toString() );
    }

  }
}

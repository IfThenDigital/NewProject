<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Advanced Custom Fields Blocks - IfThen []
 * Description:     Custom blocks built with ACF blocks framework.
 * Author:          IfThen []
 * Author URI:      www.ifthen.com
 * Text Domain:     it-acf-blocks
 * Domain Path:     /languages
 * Version:         1.0.0
 */

defined('ABSPATH') or exit;

// Plugin classes are autoloaded via composer at the root of the application.
require_once plugin_dir_path(__FILE__) . '../../../../vendor/autoload.php';

use IfThen\Acf\Blocks\Block\HeroBlock;
use IfThen\Acf\Blocks\Block\HtmlBlock;
use IfThen\Acf\Blocks\Block\ProductListWithFiltersBlock;
use IfThen\Acf\Blocks\Block\TwoColumn;
use IfThen\Acf\Blocks\Block\MosaicBlock;
use IfThen\Acf\Blocks\Block\WysiwygBlock;
use IfThen\Acf\Blocks\BlockCategory\LiberatorBlockCategory;
use IfThen\Acf\Blocks\Service\BlockManager;

class IfThenAcfBlocks {

  public function __construct() {

    add_action( 'init', function() {
    	new HtmlBlock();

      add_menu_page(
        esc_html__( 'Reusable Blocks', 'reusable-blocks-admin-menu-option' ),
        esc_html__( 'Reusable Blocks', 'reusable-blocks-admin-menu-option' ),
        'read',
        'edit.php?post_type=wp_block',
        '',
        'dashicons-block-default',
        21
      );
    } );

  }

}

new IfThenAcfBlocks();
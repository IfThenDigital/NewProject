<?php


namespace WABE\Theme;

use Timber\Site;
use Timber\Timber;

class BaseSite extends Site {

  public function __construct() {
    // Sets the directories (inside your theme) to find .twig files
    Timber::$dirname = array( 'templates', 'views' );

    // By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
    // No prob! Just set this value to true
    Timber::$autoescape = false;

    // Add filters.
    add_filter( 'crop_thumbnails_image_sizes', array( $this, 'override_crop_thumbnails_behavior' ) );
    add_filter( 'intermediate_image_sizes_advanced', array( $this, 'disable_core_image_sizes' ) );

    // Add actions.
    add_action( 'init', array( $this, 'cleanup_wp_head'));
    add_action( 'init', array( $this, 'remove_image_sizes' ));
    add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );

    parent::__construct();
  }

  /**
   * This filter call makes all image sizes work with the `crop-thumbnails` plugin.
   *
   * @param $image_sizes
   *
   * @return mixed
   */
  public function override_crop_thumbnails_behavior( $image_sizes ) {
    // The `crop-thumbnails` plugin only allows for cropping on image sizes
    // that have crop=TRUE. We can get around this restriction with the
    // `crop_thumbnails_image_sizes` filter. This filter is only called by
    // the `crop-thumbnails` plugin, and can be used to manipulate the image
    // sizes before the plugin checks them for use.

    // We do not use the default image sizes.
    // Ensure they don't show up in the list of available crop-able sizes.
    $do_not_crop_sizes = array(
      'thumbnail',
      'medium',
      'medium_large',
      'large',
    );

    foreach ( $image_sizes as $image_size_name => $image_size_data ) {
      // Restrict some defined image sizes from being crop-able.
      if ( ! in_array( $image_size_name, $do_not_crop_sizes ) ) {
        $image_sizes[ $image_size_name ]['crop'] = true;
      } else {
        $image_sizes[ $image_size_name ]['crop'] = false;
      }
    }

    return $image_sizes;
  }

  public function cleanup_wp_head() {
    add_action('wp_head', function() {
      echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0 Feed" href="'.get_bloginfo('rss2_url').'" />';
    });
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'rsd_link');
    remove_action( 'wp_head', 'wlwmanifest_link');
    remove_action( 'wp_head', 'wp_generator');
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links');
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

    // Remove emoji junk from header
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    wp_dequeue_style('style.css');
  }

  public function theme_supports() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
      'html5',
      array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
      )
    );

    /*
     * Enable support for Post Formats.
     *
     * See: https://codex.wordpress.org/Post_Formats
     */
    add_theme_support(
      'post-formats',
      array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
      )
    );

    add_theme_support( 'menus' );
  }

  /**
   * Removes additional core image sizes.
   */
  public function remove_image_sizes() {
    // Remove image sizes provided by WP core.
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
  }

  /**
   * Disables core image sizes.
   *
   * @param $sizes
   *   The current image sizes.
   *
   * @return array
   *   The modified array of image sizes.
   */
  public function disable_core_image_sizes($sizes) {
    unset( $sizes[ 'thumbnail' ]);
    unset( $sizes[ 'medium' ]);
    unset( $sizes[ 'medium_large' ]);
    unset( $sizes[ 'large' ]);

    return $sizes;
  }

}

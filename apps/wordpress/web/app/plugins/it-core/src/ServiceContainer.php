<?php


namespace IfThen\Core;

use IfThen\Utility\Utility\FileClassInfoFinder;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

/**
 * The core service container.
 */
class ServiceContainer {

  protected static $instance = null;

  protected $services = array();

  /**
   * Searches plugin directories for service providers, and registers any
   * services provided.
   */
  protected function __construct() {
    // We'll need this for getting file class information later.
    $file_class_info_finder = new FileClassInfoFinder();

    // Scan all plugin directories and look for php files with 'ServiceProvider' in the file name.
    $wp_plugin_dir = WP_PLUGIN_DIR;
    $plugin_dirs = scandir( $wp_plugin_dir );

    // Loop through all plugin directories.
    foreach ( $plugin_dirs as $plugin_dir_name ) {
      // We're only interested in the /src folder within a plugin directory.
      $plugin_src_folder = "{$wp_plugin_dir}/{$plugin_dir_name}/src";

      if ( is_dir( $plugin_src_folder ) ) {
        $src_dir_iterator = new RecursiveDirectoryIterator( $plugin_src_folder, \FilesystemIterator::SKIP_DOTS );
        $found_service_provider = false;

        foreach ( new RecursiveIteratorIterator( $src_dir_iterator ) as $file) {
          /* @var \SplFileInfo $file */
          if ( $file->getExtension() == 'php' && str_ends_with($file->getBasename( '.php' ), 'ServiceProvider' ) ) {
            $class_name = $file_class_info_finder->getClassFullNameFromFile( $file->getRealPath() );

            if ( class_exists( $class_name ) ) {
              $interfaces = class_implements( $class_name );

              if ( in_array( 'IfThen\Core\ServiceProviderInterface', $interfaces ) ) {
                // We have a service provider class implementing the correct interface!
                // Instantiate and pass this instance.
                /* @var ServiceProviderInterface $service_provider */
                $service_provider = new $class_name();
                $service_provider->register( $this );

                $found_service_provider = true;
              }
            }
          }

          // If we've located a service provider in this plugin directory, then stop
          // the search and move onto the next plugin directory.
          if ($found_service_provider) {
            break;
          }
        }
      }
    }
  }

  /**
   * Returns the service container instance.
   *
   * @return ServiceContainer
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceContainer();
    }

    return self::$instance;
  }

  /**
   * Retrieves the service by id.
   *
   * @param $id
   *   The unique id of the service.
   *
   * @return mixed
   *   The service.
   */
  public function get( $id ) {
    $service = null;

    if ( array_key_exists( $id, $this->services ) ) {
      $service = $this->services[$id];
    }

    return $service;
  }

  /**
   * Sets the provided service with the given id.
   *
   * @param $id
   *   The unique id of the service.
   * @param $service
   *   A service.
   */
  public function set( $id, $service ) {
    $this->services[$id] = $service;
  }

}
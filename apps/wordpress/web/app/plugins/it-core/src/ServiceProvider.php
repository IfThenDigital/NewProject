<?php


namespace IfThen\Core;


class ServiceProvider {

  /**
   * @var ServiceContainer
   */
  protected static $container;


  public static function init() {
    // Initialize the service container.
    static::$container = ServiceContainer::get_instance();
  }

  public static function service( $serviceId ) {
    return static::$container->get( $serviceId );
  }

}
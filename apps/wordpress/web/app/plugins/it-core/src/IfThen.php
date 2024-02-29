<?php


namespace IfThen\Core;


class IfThen {

  /**
   * @var ServiceContainer
   */
  protected static $container;

  public static function init() {
    // Initialize the service container.
    static::$container = ServiceContainer::get_instance();
  }

  public static function service( $service_id ) {
    return static::$container->get( $service_id );
  }

}
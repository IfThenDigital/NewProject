<?php

namespace IfThen\Core;

/**
 * A service provider class will implement this interface in order to provide
 * services to the service container.
 *
 * Only one service provider may exist per plugin, and the service provider must:
 *   - Have a class name ending in 'ServiceProvider'
 *   - Have the service provider placed in the 'src' folder of the plugin.
 *
 * @see IfThenCoreServiceProvider
 *
 * @package IfThen\Core
 */
interface ServiceProviderInterface {

  /**
   * @param ServiceContainer $container
   *
   * @return void
   */
  public function register( ServiceContainer &$container );

}
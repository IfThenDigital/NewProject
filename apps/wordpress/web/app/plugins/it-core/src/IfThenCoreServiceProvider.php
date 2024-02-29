<?php

namespace IfThen\Core;

use IfThen\Core\Database\PostDataAccess;
use IfThen\Core\Service\CardService;
use IfThen\Core\Service\PostService;
use IfThen\Pantheon\Cache\PantheonCache;

class IfThenCoreServiceProvider implements ServiceProviderInterface {

  /**
   * Registers services with the service provider.
   *
   * @param ServiceContainer $container
   */
  public function register( ServiceContainer &$container ) {
    // Initialize services and pass the reference to the service container.
    $container->set('ifthen.post_data_access', new PostDataAccess());
    $container->set('ifthen.post_service', new PostService());
    $container->set('ifthen.card_service', new CardService());
    $container->set('ifthen.cache', new PantheonCache());
  }

}

<?php

namespace IfThen\Pantheon\Cache;

require_once WPMU_PLUGIN_DIR . '/pantheon/pantheon-page-cache.php';

use IfThen\Core\Cache\CacheInterface;
use Pantheon_Cache;

class PantheonCache implements CacheInterface {

  /**
   * @param array $urls
   *
   * @return void
   */
  public function invalidate_urls( array $urls ) {
    Pantheon_Cache::instance()->enqueue_urls( $urls );
  }

  /**
   * @param array $term_ids
   *
   * @return void
   */
  public function invalidate_terms(array $term_ids) {
    // TODO: Implement invalidate_terms() method.
  }

  /**
   * @param array $post_ids
   *
   * @return void
   */
  public function invalidate_posts(array $post_ids) {
    // TODO: Implement invalidate_posts() method.
  }

}

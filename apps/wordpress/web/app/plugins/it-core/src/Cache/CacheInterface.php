<?php

namespace IfThen\Core\Cache;

interface CacheInterface {

  /**
   * @param array $urls
   *
   * @return void
   */
  public function invalidate_urls( array $urls );

  /**
   * @param array $term_ids
   *
   * @return void
   */
  public function invalidate_terms( array $term_ids );

  /**
   * @param array $post_ids
   *
   * @return void
   */
  public function invalidate_posts( array $post_ids );
}

<?php

namespace IfThen\Core\Service;

use IfThen\Core\Database\PostDataAccess;
use IfThen\Core\ServiceProvider;
use Timber\Post;
use WP_Post;

class PostService {

  /**
   * Get a result set of posts.
   *
   * @param array $postSearchOptions
   *
   * @return array
   *
   * @throws \Exception
   */
  public function get_posts( array $postSearchOptions ) {
    /* @var PostDataAccess $post_data_access_service */
    $post_data_access_service = ServiceProvider::service('ifthen.post_data_access');

    $result_set = $post_data_access_service->get_posts($postSearchOptions);

    return $result_set;
  }

}

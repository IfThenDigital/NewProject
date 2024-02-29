<?php

namespace IfThen\Core\Service;

use IfThen\Core\ServiceProvider;

class CardService {

  /**
   * Get post information converted into 'card' data format.
   * Posts retrieved are determined based on the post search options.
   *
   * @param array $postSearchOptions
   *   An array of search criteria.
   *
   * @return array
   *   Result set including posts and cards.
   */
  public function get_cards( array $postSearchOptions ) {
    //* @var PostService $post_service *//
    $post_service = ServiceProvider::service('ifthen.post_service');

    $result_set = $post_service->get_posts( $postSearchOptions );

    // Convert the posts result set into a cards data set.
    $cards = $this->convert_to_cards( $result_set['posts'] );

    $result_set['cards'] = $cards;

    return $result_set;
  }

  /**
   * Get card data from an existing dataset. These datasets can
   * notably include 'custom' cards as well as posts.
   *
   * @param array $data
   */
  public function get_cards_from_data( array $data ) {

  }

  protected function convert_to_cards( array $data ) {

    return $data;
  }

}

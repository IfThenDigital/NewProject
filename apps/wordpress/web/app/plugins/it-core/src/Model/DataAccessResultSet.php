<?php


namespace IfThen\Core\Model;

use IfThen\Utility\Utility\JsonSerializer;

/**
 * A result set returned by the data access layer.
 */
class DataAccessResultSet {

  protected $posts;
  protected $total_posts;
  protected $total;
  protected $filters = array();

  use JsonSerializer;

  /**
   * @return mixed
   */
  public function getPosts() {
    return $this->posts;
  }

  /**
   * @param mixed $posts
   */
  public function setPosts($posts): void {
    $this->posts = $posts;
  }

  /**
   * @return mixed
   */
  public function getTotalPosts() {
    return $this->total_posts;
  }

  /**
   * @param mixed $total_posts
   */
  public function setTotalPosts($total_posts): void {
    $this->total_posts = $total_posts;
  }

  /**
   * @return mixed
   */
  public function getTotal() {
    return $this->total;
  }

  /**
   * @param mixed $total
   */
  public function setTotal($total): void {
    $this->total = $total;
  }

  /**
   * @return mixed
   */
  public function getFilters() {
    return $this->filters;
  }

  /**
   * @param mixed $filters
   */
  public function setFilters($filters): void {
    $this->filters = $filters;
  }

  public function addFilter( $filter_name, $filter_value ) {
    $this->filters[ $filter_name ] = $filter_value;
  }

}

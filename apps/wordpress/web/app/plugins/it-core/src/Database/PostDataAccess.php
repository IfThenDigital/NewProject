<?php

namespace IfThen\Core\Database;

use IfThen\Core\Model\DataAccessResultSet;
use stdClass;
use Timber\Post;
use WP_Post;
use WP_Query;

class PostDataAccess {

  /**
   * Get a result set of post based on search options passed in.
   *
   * @param $searchOptions
   *   An array of search options. Expected data structure:
   *   [
   *     'post_types' => ['post', 'page'],
   *     'taxonomy_filters' => [
   *        [
   *          'taxonomy' => 'topic',
   *          'term_ids' => [ 5, 12, 14 ],
   *        ],
   *        [
   *          'taxonomy' => 'post_tag',
   *          'term_ids' => [ 1, 4 ],
   *        ],
   *     ],
   *     'options' => [
   *       'posts_per_page' => 4,
   *       'offset' => 0,
   *       'orderby' => 'modified',
   *       'order' => 'desc',
   *       'events' => [
   *         'type' => 'future'
   *       ],
   *     ],
   *   ]
   *
   * @return DataAccessResultSet
   *   Result set with meta information.
   *
   * @throws \Exception
   */
  public function get_posts(array $searchOptions) {
    // Build the taxonomy portion of the WP_Query.
    $taxonomy_query = [];
    if (isset($searchOptions['taxonomy_filters'])) {
      foreach ($searchOptions['taxonomy_filters'] as $filter) {
        $taxonomy_query[] = [
          'taxonomy' => $filter['taxonomy'],
          'field' => 'term_id',
          'terms' => $filter['term_ids'],
          'operator' => 'AND',
        ];
      }
    }

    // Set query defaults.
    $posts_per_page = -1;
    $offset = 0;

    // Check for a posts per page option.
    if (isset($searchOptions['options']['posts_per_page'])) {
      $posts_per_page = $searchOptions['options']['posts_per_page'];
    }

    // Check for an offset.
    if (isset($searchOptions['options']['offset'])) {
      $offset = $searchOptions['options']['offset'];
    }

    $post_types = null;
    if (isset($searchOptions['post_types'])) {
      if (is_array($searchOptions['post_types'])) {
        if (count($searchOptions['post_types']) == 1) {
          $post_types = $searchOptions['post_types'][0];
        }
        else {
          $post_types = $searchOptions['post_types'];
        }
      }
      else {
        $post_types = $searchOptions['post_types'];
      }
    }

    // Construct the query.
    $query = [
      'post_type' => $post_types,
      'posts_per_page' => $posts_per_page,
      'offset' => $offset,
      'orderby' => array(
	      'post_date' => 'DESC',
      ),
    ];

    if ((is_array($post_types) && in_array('tribe_events', $post_types)) || $post_types == 'tribe_events') {

      $event_type = 'future';
      if (isset($searchOptions['options']['events']) && isset($searchOptions['options']['events']['type'])) {
        $event_type = $searchOptions['options']['events']['type'];
      }

      if  ($event_type == 'future') {
        $event_order = 'ASC';
      }
      else {
        $event_order = 'DESC';
      }


      $query['meta_query'] = $this->get_events_meta_query($event_type);

      $query['orderby'] = array(
        'event_date' => $event_order,
        'post_date' => 'DESC',
      );
    }

    if ($taxonomy_query) {
      $query['tax_query'] = [
        'relation' => 'AND',
        $taxonomy_query,
      ];
    }

    $wp_query = new WP_Query($query);

    $result_set = new DataAccessResultSet();
    $result_set->setPosts( $this->convert_posts( $wp_query->get_posts() ) );
    $result_set->setTotalPosts( $wp_query->found_posts );
    $result_set->setTotal( $wp_query->post_count );
    $result_set->addFilter( 'post_types', isset($searchOptions['post_types']) ? $searchOptions['post_types'] : '' );
    $result_set->addFilter( 'taxonomies', isset($searchOptions['taxonomy_filters']) ? $searchOptions['taxonomy_filters'] : '' );

    return $result_set;
  }

  public function get_posts_by_id($post_ids) {
    $query = [
      'post_type' => 'any',
      'post__in' => $post_ids,
      'posts_per_page' => -1,
      'orderby' => 'date',
      'order' => 'DESC',
    ];

    $wp_query = new WP_Query($query);

    return [
      'posts' => $this->convert_posts( $wp_query->get_posts() ),
      'total_posts' => $wp_query->found_posts,
    ];
  }

	/**
	 * Builds a meta query specifically for tribe_events posts.
	 *
	 * @param string $events_type
	 *   'future' or 'past'
	 *
	 * @return array
	 *   The constructed meta query.
	 *
	 * @throws \Exception
	 */
  protected function get_events_meta_query($events_type = 'future') {
    $meta_query = array();

    // Get the current time based on the site's timezone.
    // The _EventEndDate we are querying against is based on the same timezone.
    $wp_timezone = new \DateTimeZone(get_option('timezone_string'));
    $end_time = new \DateTime('now', $wp_timezone);

    // We're wanting all events that end no more than two hours after the current time.
    $end_time->sub(new \DateInterval('PT2H'));

    if ($events_type == 'future') {

      $meta_query = array(
        'relation' => 'OR',
        'event_date' => array(
          'key' => '_EventEndDate',
          'value' => $end_time->format('Y-m-d H:i:s'),
          'compare' => '>=',
          'type' => 'DATETIME',
        ),
        'has_empty_date' => array(
          'key' => '_EventEndDate',
          'compare' => '=',
          'value' => '',
        ),
        'has_no_date' => array(
          'key' => '_EventEndDate',
          'compare' => 'NOT EXISTS',
        ),
      );
    }
    else {

      $meta_query = array(
        'relation' => 'OR',
        'event_date' => array(
          'key' => '_EventEndDate',
          'value' => $end_time->format('Y-m-d H:i:s'),
          'compare' => '<',
          'type' => 'DATETIME',
        ),
        'has_empty_date' => array(
          'key' => '_EventEndDate',
          'compare' => '=',
          'value' => '',
        ),
        'has_no_date' => array(
          'key' => '_EventEndDate',
          'compare' => 'NOT EXISTS',
        ),
      );

    }

    return $meta_query;
  }

  /**
   * Returns a single object of wp_post table row values.
   *
   * @param $slug
   *   The slug to search for.
   * @param $post_type
   *   The post type to search for.
   *
   * @return stdClass|null
   */
  public function get_post_by_slug( $slug, $post_type ) {
    global $wpdb;

    $sql = <<<QUERY
            SELECT 
                ID
            FROM 
                {$wpdb->posts}
            WHERE
                post_name = '%s'
                AND post_type = '%s';
QUERY;

    $sql = $wpdb->prepare( $sql, $slug, $post_type );

    $results = $wpdb->get_results( $sql );

    $post = null;
    if  ( $results != null && !empty( $results ) ) {
      $post = $results[0];
    }

    return $post;
  }

  /**
   * Converts WP_Post objects to Post objects.
   *
   * @param array $posts
   *   An array of WP_Post objects.
   *
   * @return array
   *   The converted array of posts.
   */
  protected function convert_posts(array $posts ) {
    $converted_posts = array();

    /* @var WP_Post $WP_Post */
    foreach ($posts as $WP_Post) {
      $converted_posts[] = new Post($WP_Post);
    }

    return $converted_posts;
  }

}

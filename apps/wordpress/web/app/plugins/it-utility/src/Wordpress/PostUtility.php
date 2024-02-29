<?php

namespace IfThen\Utility\Wordpress;

class PostUtility {

  public static function alterPageRestEndpoint() {
    // Let's alter rest endpoint queries for `page` post types.
    add_filter('rest_page_query', function($args, $request) {
      // We only want to alter rest queries for logged in users.
      if (is_user_logged_in()) {
        // We're going to increase the pagination limit for an REST calls for
        // page related information. This is particularly relevant for using the Gutenberg editor.
        //
        // This is primarily to address a bug where the Gutenberg editor loads the parent hierarchy of
        // pages, but through paged requests. For some reason, page requests return an incomplete data set.
        // Having the entire set returned in one request (no page limit) does not experience this issue.
        $args['posts_per_page'] = 1000;
      }

      return $args;
    }, 20, 2);
  }
}
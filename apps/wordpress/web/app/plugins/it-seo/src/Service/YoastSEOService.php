<?php


namespace IfThen\SEO\Service;


class YoastSEOService {

  public static function disable_yoast_robots_meta() {
    add_filter( 'wpseo_robots', '__return_false' );
  }

  /**
   * Due to the inefficient way Yoast tries to cache admin display related information,
   * this will prevent some post types with a lot of posts from producing an out of memory error.
   */
    public static function disable_yoast_admin_columns_caching()
    {
        if (function_exists('YoastSEO')) {
            $yoast_column_cache_service = YoastSEO()->classes->container->get('Yoast\\WP\\SEO\\Integrations\\Admin\\Admin_Columns_Cache_Integration');

            remove_action('manage_posts_extra_tablenav', array($yoast_column_cache_service, 'maybe_fill_cache'));
        }
    }
}

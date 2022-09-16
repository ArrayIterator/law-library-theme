<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_sitemap_stylesheet_index_url')) {
    /**
     * Replace sitemap-index.xsl
     *
     * @param string $url
     *
     * @return string
     */
    function law_lib_sitemap_stylesheet_index_url($url)
    {
        // url to sitemap-index.xsl
        return get_theme_file_uri('assets/xsl/sitemap-index.xsl');
    }
}
add_filter('wp_sitemaps_stylesheet_index_url', 'law_lib_sitemap_stylesheet_index_url');

if (!function_exists('law_lib_sitemap_stylesheet_url')) {
    /**
     * Replace sitemap.xsl
     *
     * @param string $url
     *
     * @return string
     */
    function law_lib_sitemap_stylesheet_url($url)
    {
        // url to sitemap-index.xsl
        return get_theme_file_uri('assets/xsl/sitemap.xsl');
    }
}
add_filter('wp_sitemaps_stylesheet_url', 'law_lib_sitemap_stylesheet_url');

if (!function_exists('law_lib_sitemap_wp_sitemaps_index_entry')) {
    function law_lib_sitemap_wp_sitemaps_index_entry($sitemap_entry, $object_type, $type, $page)
    {
        $args = [
            'post_status' => 'publish',
            'order_by' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 1,
        ];

        if ($object_type === 'term') {
            $args['tax_query'] = [
                'taxonomy' => $type,
            ];
        } else {
            $args['post_type'] = $type;
        }

        $posts = get_posts($args);
        foreach ($posts as $post) {
            $sitemap_entry = law_lib_sitemap_sitemaps_posts_entry(
                $sitemap_entry,
                $post,
                $post->post_type
            );
            break;
        }

        return $sitemap_entry;
    }
}
add_action('wp_sitemaps_index_entry', 'law_lib_sitemap_wp_sitemaps_index_entry', 10, 4);

if (!function_exists('law_lib_sitemap_sitemaps_posts_entry')) {
    function law_lib_sitemap_sitemaps_posts_entry($sitemap_entry, $post, $post_type)
    {
        /**
         * @var WP_Post $post
         */
        $sitemap_entry['lastmod'] = get_gmt_from_date($post->post_date, 'c');
        $medias = get_attached_media('image', $post->ID);
        if (!empty($medias)) {
            foreach ($medias as $_post) {
                $sitemap_entry['image'][] = [
                    'loc' => wp_get_attachment_url($_post->ID),
                    'title' => $_post->post_title,
                    'caption' => $_post->post_excerpt,
                ];
            }
        }

        return $sitemap_entry;
    }
}
add_filter('wp_sitemaps_posts_entry', 'law_lib_sitemap_sitemaps_posts_entry', 10, 3);

if (!function_exists('law_lib_sitemap_sitemaps_posts_show_on_front_entry')) {
    function law_lib_sitemap_sitemaps_posts_show_on_front_entry($sitemap_entry)
    {
        $posts = get_posts([
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'order'          => 'DESC',
            'order_by'       => 'date',
        ]);

        foreach ($posts as $post) {
            $sitemap_entry = law_lib_sitemap_sitemaps_posts_entry($sitemap_entry, $post, $post->post_type);
            break;
        }

        return $sitemap_entry;
    }
}
add_action('wp_sitemaps_posts_show_on_front_entry', 'law_lib_sitemap_sitemaps_posts_show_on_front_entry');

if (!function_exists('law_lib_sitemap_sitemaps_taxonomy_entry')) {
    function law_lib_sitemap_sitemaps_taxonomy_entry($sitemap_entry, $term, $taxonomy)
    {
        $posts = get_posts([
            'posts_per_page' => 1,
            'order' => 'DESC',
            'order_by' => 'date',
            'post_status' => 'publish',
            'post_type' => 'post',
            'tax_query' => [
                'taxonomy' => $taxonomy,
                'terms' => $term,
            ]
        ]);

        foreach ($posts as $post) {
            $sitemap_entry['lastmod'] = get_gmt_from_date($post->post_date, 'c');
            break;
        }
        return $sitemap_entry;
    }
}
add_filter('wp_sitemaps_taxonomies_entry', 'law_lib_sitemap_sitemaps_taxonomy_entry', 10, 3);

if (!function_exists('law_lib_sitemap_sitemaps_user_entry')) {
    function law_lib_sitemap_sitemaps_user_entry($sitemap_entry, $user)
    {
        $posts = get_posts([
            'posts_per_page' => 1,
            'order' => 'DESC',
            'order_by' => 'date',
            'post_status' => 'publish',
            'post_type' => 'post',
            'author' => $user->ID
        ]);

        foreach ($posts as $post) {
            $sitemap_entry['lastmod'] = get_gmt_from_date($post->post_date, 'c');
            break;
        }
        return $sitemap_entry;
    }
}
add_filter('wp_sitemaps_users_entry', 'law_lib_sitemap_sitemaps_user_entry', 10, 2);

if (!function_exists('law_lib_wp_sitemaps_get_server')) {
    function law_lib_wp_sitemaps_get_server()
    {
        global $wp_sitemaps;
        if ( ! $wp_sitemaps) {
            $wp_sitemaps = new Law_Lib_Sitemap();
            $wp_sitemaps->init();
            do_action('wp_sitemaps_init', $wp_sitemaps);
        }

        return $wp_sitemaps;
    }
}
add_action('init', 'law_lib_wp_sitemaps_get_server', PHP_INT_MIN);

<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_setup_theme')) {
    function law_lib_setup_theme()
    {
        load_theme_textdomain('law-lib', get_template_directory() . '/languages');

        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'widgets-block-editor' );
        add_theme_support( 'widgets' );
        add_theme_support('amp');
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('widgets');
        add_theme_support( 'custom-mobile-logo', [
            'width'                => 100,
            'height'               => 30,
            'flex-width'           => false,
            'flex-height'          => false,
            'header-text'          => '',
            'unlink-homepage-logo' => false,
        ] );

        add_theme_support( 'custom-logo', [
            'width'                => 200,
            'height'               => 60,
            'flex-width'           => false,
            'flex-height'          => false,
            'header-text'          => '',
            'unlink-homepage-logo' => false,
        ] );

        /* NAV MENU */
        register_nav_menu('top-menu', __('Top Menu', 'law-lib'));
        register_nav_menu('main-menu', __('Main Menu', 'law-lib'));
        register_nav_menu('sidebar-menu', __('Sidebar Menu', 'law-lib'));
        register_nav_menu('mobile-menu', __('Mobile Menu', 'law-lib'));
        register_nav_menu('footer-menu', __('Footer Menu', 'law-lib'));

        set_post_thumbnail_size(
            Law_Lib_Image::POST_THUMBNAIL_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_SIZE['height']
        );

        add_image_size(
            Law_Lib_Image::POST_THUMBNAIL,
            Law_Lib_Image::POST_THUMBNAIL_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_THUMBNAIL_BIG,
            Law_Lib_Image::POST_THUMBNAIL_BIG_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_BIG_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_THUMBNAIL_WIDE,
            Law_Lib_Image::POST_THUMBNAIL_WIDE_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_WIDE_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_THUMBNAIL_WIDEST,
            Law_Lib_Image::POST_THUMBNAIL_WIDEST_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_WIDEST_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_THUMBNAIL_MINI,
            Law_Lib_Image::POST_THUMBNAIL_MINI_SIZE['width'],
            Law_Lib_Image::POST_THUMBNAIL_MINI_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_SQUARE,
            Law_Lib_Image::POST_SQUARE_SIZE['width'],
            Law_Lib_Image::POST_SQUARE_SIZE['height'],
            true
        );
        add_image_size(
            Law_Lib_Image::POST_SQUARE_MINI,
            Law_Lib_Image::POST_SQUARE_MINI_SIZE['width'],
            Law_Lib_Image::POST_SQUARE_MINI_SIZE['height'],
            true
        );

        /* SIDEBAR */
        register_sidebar([
            'id'            => 'homepage-sidebar',
            'name'          => __('Home Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'archive-sidebar',
            'name'          => __('Archive Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'search-sidebar',
            'name'          => __('Search Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'page-sidebar',
            'name'          => __('Page Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'post-sidebar',
            'name'          => __('Post Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'below-post',
            'name'          => __('Below Post', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'blog-sidebar',
            'name'          => __('Blog Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'notfound-sidebar',
            'name'          => __('404 Sidebar', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);
        register_sidebar([
            'id'            => 'footer-sidebar',
            'name'          => __('Footer Area', 'law-lib'),
            'before_widget' => '',
            'after_widget'  => "\n",
            'before_title'  => '',
            'after_title'   => "\n",
        ]);

        $_search = law_lib_component_option('search');
        $_search = !is_array($_search) ? [] : $_search;
        if (($_search['search_microdata']??'no') === 'yes') {
            add_filter( 'rank_math/json_ld/disable_search', 'law_lib_enable_schema_rank_math');
        }
    }
}

add_action('after_setup_theme', 'law_lib_setup_theme');

if (!function_exists('law_lib_enable_schema_rank_math')) {
    function law_lib_enable_schema_rank_math($res)
    {
        global $law_lib_enabled_schema_rank_math;

        // no override
        if ($law_lib_enabled_schema_rank_math === true) {
            return $res;
        }

        $law_lib_enabled_schema = true;
        return true;
    }
}

if (!function_exists('law_lib_setup_return_string_post')) {
    function law_lib_setup_return_string_post()
    {
        return 'posts';
    }
}

if (!function_exists('law_lib_setup_wp_query')) {
    /**
     * Fix No Home page
     */
    function law_lib_setup_wp_query()
    {
        global $wp_query;

        if ($wp_query->is_home()) {
            return;
        }

        if (($wp_query->query['paged']??0) < 1) {
            return;
        }

        if (is_string($wp_query->query['paged']) && preg_match('~[^0-9]~', $wp_query->query['paged'])) {
            return;
        }

        $page = absint($wp_query->query['paged']);
        $home_url = home_url('/');
        if (law_lib_logic_enable_blog_filtering()) {
            // resolve canonical
            if ($page == 1 || $page == 2) {
                if ($page === 1) {
                    $_resolve_request = function () {
                        global $wp;

                        return home_url(rtrim($wp->request, '/') . '/');
                    };

                    remove_action('template_redirect', 'redirect_canonical');
                    add_filter('get_canonical_url', $_resolve_request);
                    add_filter('rank_math/frontend/canonical', $_resolve_request);
                } else {
                    $_resolve_prev = function () {
                        global $wp;
                        $url = str_replace('/2', '/1', home_url(rtrim($wp->request, '/') . '/'));

                        return '<link rel="prev" href="' . esc_url($url) . "\" />\n";
                    };
                    add_filter('wpseo_prev_rel_link', $_resolve_prev);
                    add_filter("rank_math/frontend/prev_rel_link", $_resolve_prev);
                }
            }
            add_filter('paginate_links', function ($link) use ($home_url) {
                global $wp_rewrite;
                if ($link !== $home_url) {
                    return $link;
                }

                // Setting up default values based on the current URL.
                $page_link = html_entity_decode(get_pagenum_link());
                $url_parts = explode('?', $page_link);
                // Append the format placeholder to the base URL.
                $page_link = trailingslashit($url_parts[0]) . '%_%';
                $link      = $page_link;
                $format    = $wp_rewrite->using_index_permalinks() && ! strpos($page_link,
                    'index.php') ? 'index.php/' : '';
                $format    .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%',
                    'paged') : '?paged=%#%';
                $link      = str_replace('%_%', $format, $link);

                return str_replace('%#%', 1, $link);
            });
        }

        add_action('pre_option_show_on_front', 'law_lib_setup_return_string_post');

        $wp_query->query_vars['p']  = 0;
        $page = $wp_query->query_vars['page']??$wp_query->query_vars['paged'];
        unset($wp_query->query_vars['page']);
        $wp_query->query['page_id'] = 0;
        if ($page) {
            $wp_query->query['paged'] = $page;
        }

        $wp_query->set('post_type', 'post');
        $wp_query->query($wp_query->query);
        add_filter('template_include', 'get_index_template');
    }
}

add_action('wp', 'law_lib_setup_wp_query');

if (!function_exists('law_lib_setup_microdata')) {
    function law_lib_setup_microdata()
    {
        global $law_lib_enabled_schema;
        $_search = law_lib_component_option('search');
        $_search = !is_array($_search) ? [] : $_search;
        if ($law_lib_enabled_schema) {
            return;
        }
        if (($_search['search_microdata']??'no') !== 'yes') {
            return;
        }
        $link = home_url('?s={search_term_string}');
        $link = str_replace(['%7B', '%7D'], ['{', '}'], $link);
        $json = [
            "@context"        => "https://schema.org",
            "@type"           => "WebSite",
            "@id"             => home_url('#website'),
            "name"            => get_bloginfo('name'),
            "url"             => home_url(),
            "potentialAction" => [
                [
                    "@type" => "SearchAction",
                    "target" => [
                       "@type" => "EntryPoint",
                        "urlTemplate" => $link
                    ],
                    "query-input" => "required name=search_term_string"
                ]
            ]
        ];
        $json = json_encode($json, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        echo "<script type=\"application/ld+json\" id='law-lib-microdata'>{$json}</script>";
    }
}

add_action('wp_head', 'law_lib_setup_microdata', 20);

if (!function_exists('law_lib_setup_pre_get_posts')) {
    /**
     * @param WP_Query $wp_query
     */
    function law_lib_setup_pre_get_posts(WP_Query $wp_query)
    {
        if (!$wp_query->is_search() || defined('REST_REQUEST')) {
            return;
        }

        $_front_post = get_option('page_on_front');
        // disable frontend search
        if (is_numeric($_front_post)) {
            $wp_query->set('post__not_in', [$_front_post]);
        }
        if (law_lib_logic_enable_blog_filtering() && empty($_REQUEST['no-filter'])) {
            $_mods       = law_lib_component_option('search');
            $_mods       = ! is_array($_mods) ? [] : $_mods;
            $_mods       = $_mods['categories_exclude_search'] ?? [];
            $_mods       = law_lib_component_get_all_category_as_key_name_sanitize($_mods);
            if (empty($_mods)) {
                return;
            }
            $wp_query->set('category__not_in', $_mods);
            $wp_query->set('post_type', 'post');
        }
    }
}

add_action('pre_get_posts', 'law_lib_setup_pre_get_posts');

if (!function_exists('law_lib_setup_set_handler_search_not_found')) {
    /**
     * @param WP_Post[] $posts
     * @param WP_Query $wp_query
     *
     * @return array
     */
    function law_lib_setup_set_handler_search_not_found(array $posts, WP_Query $wp_query): array
    {
        if ( ! $wp_query->is_search()) {
            return $posts;
        }

        if (empty($posts)) {
            status_header(404);
            $wp_query->set_404();
            $wp_query->is_search = true;
        }
        return $posts;
    }
}
add_filter('the_posts', 'law_lib_setup_set_handler_search_not_found', 10, 2);

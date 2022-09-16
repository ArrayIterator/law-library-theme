<?php
if ( ! class_exists('WP_REST_Posts_Controller')) {
    return;
}

if ( ! class_exists('Law_Lib_Post_Rest')) {
    class Law_Lib_Post_Rest extends WP_REST_Posts_Controller
    {
        public function __construct($post_type)
        {
            parent::__construct($post_type);
            $this->namespace = 'law-lib';
        }

        public function register_routes()
        {
            parent::register_routes();
            $get_item_args = array(
                'context' => $this->get_context_param( array( 'default' => 'view' ) ),
            );
            register_rest_route(
                $this->namespace,
                '/popular',
                array(
                    'args'   => [
                        'id' => [
                            'description' => __( 'Unique identifier for the post.', 'law-lib' ),
                            'type'        => 'integer',
                        ],
                    ],
                    [
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => [$this, 'get_popular_items'],
                        'permission_callback' => '__return_true',
                        'args'                => $get_item_args,
                    ],
                    'schema' => [$this, 'get_public_item_schema'],
                ),
                true
            );
            register_rest_route(
                $this->namespace,
                '/views-set',
                array(
                    'args'   => [
                        'id' => [
                            'description' => __( 'Unique identifier for the post.', 'law-lib' ),
                            'type'        => 'integer',
                        ],
                    ],
                    [
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => [$this, 'set_posts'],
                        'permission_callback' => '__return_true',
                    ],
                ),
                true
            );
        }

        public function set_posts($request)
        {
            $id = $request->get_param('pid');
            $hash = $request->get_param('hash');
            $id = is_numeric($id) ? $id : null;
            if (!$id) {
                return new WP_Error(
                    'rest_no_post_defined',
                    __('You need to define an id to set views.', 'law-lib'),
                    ['status' => 400]
                );
            }

            $error = new WP_Error(
                'rest_post_invalid_hash',
                __( 'Invalid post verification.', 'law-lib' ),
                array( 'status' => 400 )
            );
            if (!$hash
                || !is_string($hash)
                || !hash_equals(
                    $hash,
                    hash_hmac(
                        'sha1', $id, SECURE_AUTH_SALT . SECURE_AUTH_KEY . date('Y-m')
                    )
                )
            ) {
                return $error;
            }

            $ref = wp_get_referer();
            if ( ! $ref ) {
                return $error;
            }
            $domain = wp_parse_url( site_url() )['host'];
            if ( ! preg_match( '~https?://' . preg_quote( $domain, '~' ) . '/~', $ref ) ) {
                return $error;
            }

            $post = get_post($id);
            if ( ! $post
                 || $post->ID < 1 || $post->post_status !== 'publish'
                || $post->post_type !== 'post'
            ) {
                return new WP_Error(
                    'rest_post_invalid_id',
                    __( 'Invalid post ID.', 'law-lib' ),
                    array( 'status' => 404 )
                );
            }

            $cookieName = 'law-lib-pvw';
            $name       = apply_filters(
                'law_lib_handler_rest_post_views_cookie_name',
                $cookieName,
                $post
            );
            $cookies    = $_COOKIE[ $name ] ?? '';

            $_id          = [];
            $original_ids = [];
            if ( is_string( $cookies ) ) {
                $ids          = explode( '.', $cookies );
                $_id          = array_filter( array_map( 'trim', $ids ), 'is_numeric' );
                $_id          = array_values( array_unique( $_id ) );
                $original_ids = array_map( '__return_true', array_flip( $_id ) );
                if ( ! empty( $_id ) ) {
                    $args = [
                        'post__in'         => $_id,
                        'per_page'         => 50,
                        'post_type'        => 'post',
                        'post_status'      => 'publish',
                        'has_password'     => false,
                        'suppress_filters' => true,
                        'no_found_rows'    => true,
                    ];
                    $_id  = [];
                    foreach ( ( new WP_Query )->query( $args ) as $post ) {
                        $_id[ $post->ID ] = true;
                    }
                }
            }
            $set = false;
            if ( ! isset( $_id[ $id ] ) ) {
                do_action( 'law_lib_rest_popular_before_process' . __FUNCTION__, $id, $_id, $original_ids );
                $set = true;
                Law_Lib_Popular_Views::getInstance()->setData($id);
                $_id[ $id ] = true;
                do_action( 'law_lib_rest_popular_after_process' . __FUNCTION__, $id, $_id, $original_ids );
            }

            if ( ! headers_sent() && array_diff_key( $_id, $original_ids ) ) {
                do_action( 'law_lib_rest_popular_before_process' . __FUNCTION__, $id, $_id, $original_ids );
                setcookie( $name, implode( '.', array_keys( $_id ) ), strtotime( '+7 days' ), '/' );
            }
            return rest_ensure_response([
                'id' => (int) $id,
                'set' => $set
            ]);
        }

        public function get_item($request)
        {
            $response                  = parent::get_item($request);
            $response                  = Law_Lib_Rest_Response::fromResponse($response);
            $response->single_response = true;

            return $response;
        }

        private function prepare_tax_query(array $args, WP_REST_Request $request)
        {
            $relation = $request['tax_relation'];

            if ($relation) {
                $args['tax_query'] = ['relation' => $relation];
            }

            $taxonomies = wp_list_filter(
                get_object_taxonomies($this->post_type, 'objects'),
                ['show_in_rest' => true]
            );

            foreach ($taxonomies as $taxonomy) {
                $base = ! empty($taxonomy->rest_base) ? $taxonomy->rest_base : $taxonomy->name;

                $tax_include = $request[$base];
                $tax_exclude = $request[$base . '_exclude'];
                if ($tax_include) {
                    $terms            = [];
                    $include_children = (bool) ($request['include_children']??false);
                    $operator         = 'IN';

                    if (rest_is_array($tax_include)) {
                        $terms = $tax_include;
                    } elseif (rest_is_object($tax_include)) {
                        $terms            = empty($tax_include['terms']) ? [] : $tax_include['terms'];
                        $include_children = ! empty($tax_include['include_children']);

                        if (isset($tax_include['operator']) && 'AND' === $tax_include['operator']) {
                            $operator = 'AND';
                        }
                    }

                    if ($terms) {
                        $args['tax_query'][] = [
                            'taxonomy'         => $taxonomy->name,
                            'field'            => 'term_id',
                            'terms'            => $terms,
                            'include_children' => $include_children,
                            'operator'         => $operator,
                        ];
                    }
                }

                if ($tax_exclude) {
                    $terms            = [];
                    $include_children = false;

                    if (rest_is_array($tax_exclude)) {
                        $terms = $tax_exclude;
                    } elseif (rest_is_object($tax_exclude)) {
                        $terms            = empty($tax_exclude['terms']) ? [] : $tax_exclude['terms'];
                        $include_children = ! empty($tax_exclude['include_children']);
                    }

                    if ($terms) {
                        $args['tax_query'][] = [
                            'taxonomy'         => $taxonomy->name,
                            'field'            => 'term_id',
                            'terms'            => $terms,
                            'include_children' => $include_children,
                            'operator'         => 'NOT IN',
                        ];
                    }
                }
            }

            return $args;
        }

        /**
         * @param WP_REST_Request $request
         */
        public function get_popular_items($request)
        {
            $day = $request->get_param('day');
            $day = is_numeric($day) ? absint($day) : 30;
            $num = $request->get_param('num');
            $num = is_numeric($num) ? absint($num) : 5;
            $page = $request->get_param('page');
            $page = is_numeric($page) ? absint($page) : 1;
            $page = $page < 1 ? 1 : $page;
            $popular = Law_Lib_Meta_Query::popularPosts($day,
                $num,
                $page
            );
            $request_params = [
                'day' => $day,
                'num' => $num,
            ];
            $base           = add_query_arg(urlencode_deep($request_params),
                rest_url(sprintf('%s/%s', $this->namespace, 'popular')));
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
            $posts = [];
            foreach ($popular->posts as $post) {
                $primary_cat    = Law_Lib_Meta_Data::primaryCategory($post->ID);
                if ($primary_cat) {
                    $primary_cat->link = get_category_link($primary_cat);
                }

                $post->primary_category = $primary_cat;
                $tags_array = [];
                $tags       = [];
                /**
                 * @var WP_Term $cat
                 */
                foreach (get_the_tags($post) as $tag) {
                    $tags_array[$tag->term_id] = $tag->name;
                }
                foreach ($tags_array as $sort => $tag_id) {
                    $tags[$sort] = [
                        'id'   => $tag_id,
                        'name' => $tags_array[$tag_id] ?? '',
                    ];
                }
                $post->tag_names = $tags;

                $datetime = date_create($post->date_gmt, new DateTimeZone('UTC'));
                $post->date_gmt_format = $datetime->format($date_format);
                $post->time_gmt_format = $datetime->format($time_format);
                $datetime  = date_create($post->date, wp_timezone());
                $post->date_format = $datetime->format($date_format);
                $post->time_format = $datetime->format($time_format);
                $post->author_name = '';
                $author = get_userdata($post->post_author);
                $post->author_name = $author ? $author->display_name : '';
                $_local       = get_post_datetime($post->ID);
                $_time        = sprintf(
                    '<time datetime="%1$s" class="published-date">%2$s %3$s %4$s</time>',
                    esc_attr(get_the_date('c', $post->ID)),
                    esc_html(wp_date($date_format, $_local->getTimestamp(), $_local->getTimezone())),
                    apply_filters('law_lib_date_hour_separator', '|'),
                    esc_html($_local->format($time_format))
                );
                $post->thumbnail = Law_Lib_Image::thumbnailImageAttachment(
                    $popular,
                    Law_Lib_Image::POST_SQUARE_MINI
                );
                $post->time_format = $_time;
                $post->author = [
                    'id'   => $author ? $author->ID : 0,
                    'name' => $author ? $author->display_name : '',
                    'link' => $author ? get_author_posts_url($author->ID) : '',
                ];
                $post->post_class  = implode(' ', get_post_class(! empty($posts->thumbnail) ? 'has-thumbnail' : '', $popular));
                $data    = $this->prepare_item_for_response($post, $request);
                $posts[] = $this->prepare_response_for_collection($data);
            }
            $page = $popular->current_page;
            $page = $page < 0 ? 0 : $page;
            if ($posts && $page < 1) {
                $page = 1;
            }
            $max_pages = $popular->total_page;
            $next_page = null;
            $next_link = null;
            if ($max_pages > $page) {
                $next_page = $page + 1;
                $next_link = add_query_arg('page', $next_page, $base);
            }
            $response = rest_ensure_response($posts);
            $response = Law_Lib_Rest_Response::fromResponse($response);
            $response->setMetaData('item_count', count($posts));
            $response->setMetaData('item_total', $popular->total_found_posts);
            $response->setMetaData('views_total', $popular->total_views);
            $response->setMetaData('total_page', $max_pages);
            $response->setMetaData('current_page', $page);
            $response->setMetaData('next_page', $next_page);
            $response->setMetaData('next_link', $next_link);
            $response->single_response = false;

            return $response;
        }

        public function get_items($request)
        {
            $is_search      = $request->get_param('search')
                              && $request->get_param('amp-request') === 'search'
                              && ! $request->get_param('categories_exclude');
            $is_blog        = $request->get_param('amp-request') === 'blog'
                              && ! $request->get_param('categories_exclude');
            $is_archive     = $request->get_param('amp-request') === 'archive'
                              && ! $request->get_param('categories_exclude');
            $with_thumbnail = $request->has_param('thumbnail');
            if ($is_blog || $is_search || $is_archive) {
                $offset = $request->get_param('offset');
                $offset = ! is_numeric($offset) ? null : absint($offset);
                $offset = $offset < 0 ? null : $offset;
                if ($is_search || $is_archive) {
                    $_search_options   = law_lib_component_option($is_search ? 'search' : 'archive');
                    $_search_options   = ! is_array($_search_options) ? [] : $_search_options;
                    $_category_options = $_search_options['categories_exclude'] ?? [];
                    $_category_options = law_lib_component_get_all_category_as_key_name_sanitize($_category_options);
                    if ( ! empty($_category_options)) {
                        $request['categories_exclude'] = array_values($_category_options);
                        unset($request['categories']);
                    }
                } elseif ($is_blog) {
                    $_search_options   = law_lib_component_option('homepage');
                    $_search_options   = ! is_array($_search_options) ? [] : $_search_options;
                    $_category_options = $_search_options['blog_categories'] ?? [];
                    $_category_options = law_lib_component_get_all_category_as_key_name_sanitize($_category_options);
                    if ( ! empty($_category_options)) {
                        $request['categories'] = array_values($_category_options);
                        unset($request['categories_exclude']);
                    }
                }

                if ($offset === null) {
                    $default_per_page = 10;
                    $_search_per_page = $_search_options['per_page_first'] ?? $default_per_page;
                } else {
                    $default_per_page = 5;
                    $_search_per_page = $_search_options['per_page'] ?? $default_per_page;
                }

                $_search_per_page    = ! is_numeric($_search_per_page) ? $default_per_page : absint($_search_per_page);
                $_search_per_page    = $_search_per_page < 2 ? 2 : $_search_per_page;
                $_search_per_page    = $_search_per_page > 50 ? 50 : $_search_per_page;
                $request['per_page'] = $_search_per_page;
            }

            $request['page'] = (int)($request['page'] ?? 0);
            // Ensure a search string is set in case the orderby is set to 'relevance'.
            if ( ! empty($request['orderby']) && 'relevance' === $request['orderby'] && empty($request['search'])) {
                return new WP_Error(
                    'rest_no_search_term_defined',
                    __('You need to define a search term to order by relevance.', 'law-lib'),
                    ['status' => 400]
                );
            }

            // Ensure an include parameter is set in case the orderby is set to 'include'.
            if ( ! empty($request['orderby']) && 'include' === $request['orderby'] && empty($request['include'])) {
                return new WP_Error(
                    'rest_orderby_include_missing_include',
                    __('You need to define an include parameter to order by include.', 'law-lib'),
                    ['status' => 400]
                );
            }

            // Retrieve the list of registered collection query parameters.
            $registered = $this->get_collection_params();
            $args       = [];

            /*
             * This array defines mappings between public API query parameters whose
             * values are accepted as-passed, and their internal WP_Query parameter
             * name equivalents (some are the same). Only values which are also
             * present in $registered will be set.
             */
            $parameter_mappings = [
                'author'         => 'author__in',
                'author_exclude' => 'author__not_in',
                'exclude'        => 'post__not_in',
                'include'        => 'post__in',
                'menu_order'     => 'menu_order',
                'offset'         => 'offset',
                'order'          => 'order',
                'orderby'        => 'orderby',
                'page'           => 'paged',
                'parent'         => 'post_parent__in',
                'parent_exclude' => 'post_parent__not_in',
                'search'         => 's',
                'slug'           => 'post_name__in',
                'status'         => 'post_status',
            ];

            /*
             * For each known parameter which is both registered and present in the request,
             * set the parameter's value on the query $args.
             */
            foreach ($parameter_mappings as $api_param => $wp_param) {
                if (isset($registered[$api_param], $request[$api_param])) {
                    $args[$wp_param] = $request[$api_param];
                }
            }

            // Check for & assign any parameters which require special handling or setting.
            $args['date_query'] = [];

            if (isset($registered['before'], $request['before'])) {
                $args['date_query'][] = [
                    'before' => $request['before'],
                    'column' => 'post_date',
                ];
            }

            if (isset($registered['modified_before'], $request['modified_before'])) {
                $args['date_query'][] = [
                    'before' => $request['modified_before'],
                    'column' => 'post_modified',
                ];
            }

            if (isset($registered['after'], $request['after'])) {
                $args['date_query'][] = [
                    'after'  => $request['after'],
                    'column' => 'post_date',
                ];
            }

            if (isset($registered['modified_after'], $request['modified_after'])) {
                $args['date_query'][] = [
                    'after'  => $request['modified_after'],
                    'column' => 'post_modified',
                ];
            }

            // Ensure our per_page parameter overrides any provided posts_per_page filter.
            if (isset($registered['per_page'])) {
                $args['posts_per_page'] = $request['per_page'];
            }

            if (isset($registered['sticky'], $request['sticky'])) {
                $sticky_posts = get_option('sticky_posts', []);
                if ( ! is_array($sticky_posts)) {
                    $sticky_posts = [];
                }
                if ($request['sticky']) {
                    /*
                     * As post__in will be used to only get sticky posts,
                     * we have to support the case where post__in was already
                     * specified.
                     */
                    $args['post__in'] = $args['post__in'] ? array_intersect($sticky_posts,
                        $args['post__in']) : $sticky_posts;

                    /*
                     * If we intersected, but there are no post IDs in common,
                     * WP_Query won't return "no posts" for post__in = array()
                     * so we have to fake it a bit.
                     */
                    if ( ! $args['post__in']) {
                        $args['post__in'] = [0];
                    }
                } elseif ($sticky_posts) {
                    /*
                     * As post___not_in will be used to only get posts that
                     * are not sticky, we have to support the case where post__not_in
                     * was already specified.
                     */
                    $args['post__not_in'] = array_merge($args['post__not_in'], $sticky_posts);
                }
            }

            $args = $this->prepare_tax_query($args, $request);

            // Force the post_type argument, since it's not a user input variable.
            $args['post_type'] = $this->post_type;

            /**
             * Filters WP_Query arguments when querying posts via the REST API.
             *
             * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
             *
             * Possible hook names include:
             *
             *  - `rest_post_query`
             *  - `rest_page_query`
             *  - `rest_attachment_query`
             *
             * Enables adding extra arguments or setting defaults for a post collection request.
             *
             * @param array $args Array of arguments for WP_Query.
             * @param WP_REST_Request $request The REST API request.
             *
             * @link https://developer.wordpress.org/reference/classes/wp_query/
             *
             * @since 4.7.0
             * @since 5.7.0 Moved after the `tax_query` query arg is generated.
             *
             */
            $args       = apply_filters("rest_{$this->post_type}_query", $args, $request);
            $query_args = $this->prepare_items_query($args, $request);

            $posts_query  = new WP_Query();
            $query_result = $posts_query->query($query_args);
            // Allow access to all password protected posts if the context is edit.
            if ('edit' === $request['context']) {
                add_filter('post_password_required', [$this, 'check_password_required'], 10, 2);
            }

            $posts = [];

            foreach ($query_result as $post) {
                if ( ! $this->check_read_permission($post)) {
                    continue;
                }

                $data    = $this->prepare_item_for_response($post, $request);
                $posts[] = $this->prepare_response_for_collection($data);
            }

            // Reset filter.
            if ('edit' === $request['context']) {
                remove_filter('post_password_required', [$this, 'check_password_required']);
            }

            $page        = (int)$query_args['paged'];
            $total_posts = $posts_query->found_posts;

            if ($total_posts < 1) {
                // Out-of-bounds, run the query again without LIMIT for total count.
                unset($query_args['paged']);

                $count_query = new WP_Query();
                $count_query->query($query_args);
                $total_posts = $count_query->found_posts;
            }

            $max_pages = ceil($total_posts / (int)$posts_query->query_vars['posts_per_page']);

            if ($page > $max_pages && $total_posts > 0) {
                return new WP_Error(
                    'rest_post_invalid_page_number',
                    __('The page number requested is larger than the number of pages available.', 'law-lib'),
                    ['status' => 400]
                );
            }

            $response = rest_ensure_response($posts);

            $response->header('X-WP-Total', (int)$total_posts);
            $response->header('X-WP-TotalPages', (int)$max_pages);

            $request_params = $request->get_query_params();
            $base           = add_query_arg(urlencode_deep($request_params),
                rest_url(sprintf('%s/%s', $this->namespace, $this->rest_base)));

            if ($page > 1) {
                $prev_page = $page - 1;

                if ($prev_page > $max_pages) {
                    $prev_page = $max_pages;
                }

                $prev_link = add_query_arg('page', $prev_page, $base);
                $response->link_header('prev', $prev_link);
            }
            if ($max_pages > $page) {
                $next_page = $page + 1;
                $next_link = add_query_arg('page', $next_page, $base);

                $response->link_header('next', $next_link);
            }

            $response  = Law_Lib_Rest_Response::fromResponse($response);
            $next_link = false;
            $next_page = false;
            if ($max_pages > $page) {
                $request_params = $request->get_query_params();
                $base           = add_query_arg(urlencode_deep($request_params),
                    rest_url(sprintf('%s/%s', $this->namespace, $this->rest_base)));
                $next_page      = $page + 1;
                $next_link      = add_query_arg('page', $next_page, $base);
            }

            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
            foreach ($response->data as $key => $item) {
                $response->data[$key]['category_names'] = [];
                if (isset($item['categories'])) {
                    $categories_array = [];
                    $categories       = [];
                    /**
                     * @var WP_Term $cat
                     */
                    foreach (get_categories($item['categories']) as $cat) {
                        $categories_array[$cat->term_id] = $cat->name;
                    }
                    foreach ($item['categories'] as $sort => $cat_id) {
                        $categories[$sort] = [
                            'id'   => $cat_id,
                            'name' => $categories_array[$cat_id] ?? '',
                        ];
                    }
                    $response->data[$key]['category_names'] = $categories;
                }
                $response->data[$key]['tag_names'] = [];
                if (isset($item['tags'])) {
                    $tags_array = [];
                    $tags       = [];
                    /**
                     * @var WP_Term $cat
                     */
                    foreach (get_tags($item['tags']) as $tag) {
                        $tags_array[$tag->term_id] = $tag->name;
                    }
                    foreach ($item['tags'] as $sort => $tag_id) {
                        $tags[$sort] = [
                            'id'   => $tag_id,
                            'name' => $tags_array[$tag_id] ?? '',
                        ];
                    }
                    $response->data[$key]['tag_names'] = $tags;
                }

                if (isset($item['date_gmt'])) {
                    $datetime                                = date_create($item['date_gmt'],
                        new DateTimeZone('UTC'));
                    $response->data[$key]['date_gmt_format'] = $datetime->format($date_format);
                    $response->data[$key]['time_gmt_format'] = $datetime->format($time_format);
                }

                if (isset($item['date'])) {
                    $datetime                            = date_create($item['date'], wp_timezone());
                    $response->data[$key]['date_format'] = $datetime->format($date_format);
                    $response->data[$key]['time_format'] = $datetime->format($time_format);
                }
                $response->data[$key]['author_name'] = '';
                if (isset($item['author'])) {
                    $author                              = get_userdata($item['author']);
                    $response->data[$key]['author_name'] = $author ? $author->display_name : '';
                }

                $item         = get_post($item['id']);
                $_local       = get_post_datetime($item->ID);
                $_date_format = get_option('date_format');
                $_time_format = get_option('time_format');
                $_user        = get_userdata($item->post_author);
                $_time        = sprintf(
                    '<time datetime="%1$s" class="published-date">%2$s %3$s %4$s</time>',
                    esc_attr(get_the_date('c', $item->ID)),
                    esc_html(wp_date($_date_format, $_local->getTimestamp(), $_local->getTimezone())),
                    apply_filters('law_lib_date_hour_separator', '|'),
                    esc_html($_local->format($_time_format))
                );

                if ($with_thumbnail) {
                    $thumbnail_size = $is_search
                        ? Law_Lib_Image::POST_SQUARE_MINI
                        : Law_Lib_Image::POST_THUMBNAIL;

                    $response->data[$key]['thumbnail'] = Law_Lib_Image::thumbnailImageAttachment(
                        $item,
                        $thumbnail_size
                    );
                }
                $response->data[$key]['time_format'] = $_time;
                $response->data[$key]['author']      = [
                    'id'   => $_user->ID,
                    'name' => $_user->display_name,
                    'link' => get_author_posts_url($_user->ID),
                ];
                $response->data[$key]['post_class']  = implode(' ',
                    get_post_class(! empty($response->data[$key]['thumbnail']) ? 'has-thumbnail' : '', $item));
                $primary_cat                         = Law_Lib_Meta_Data::primaryCategory($item->ID);
                if ($primary_cat) {
                    $primary_cat->link = get_category_link($primary_cat);
                }

                $response->data[$key]['primary_category'] = $primary_cat;
                if ($is_archive || $is_blog) {
                    global $post, $wp_query;
                    $wp_query = $posts_query;
                    if ($is_blog) {
                        $wp_query->has_front_page_data = true;
                    }
                    $post = $item;
                    ob_start();
                    get_template_part(
                        'templates/contents/content',
                        get_post_format($item),
                        [
                            'post_id' => $item->ID,
                        ]
                    );
                    $response->data[$key]['content'] = ob_get_clean();
                }
            }

            $response->setMetaData('item_count', count($response->data));
            $response->setMetaData('item_total', $total_posts);
            $response->setMetaData('total_page', $max_pages);
            $response->setMetaData('current_page', $page);
            $response->setMetaData('next_page', $next_page);
            $response->setMetaData('next_link', $next_link);
            $response->single_response = false;

            return $response;
        }
    }
}

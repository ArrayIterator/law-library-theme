<?php

use Automattic\Jetpack\Connection\Client;

if (!class_exists('Law_Lib_Meta_Query')) {
    class Law_Lib_Meta_Query
    {
        /**
         * @param null|WP_Post|int $postId
         * @param int $count
         * @param string|null $orderby
         * @param string $order
         * @param int $current_page
         * @param array $exclude_categories
         * @param array $exclude_tags
         *
         * @return stdClass
         */
        public static function relatedPosts(
            $postId = null,
            int $count = 10,
            string $orderby = null,
            string $order = 'desc',
            int $current_page = 1,
            array $exclude_categories = [],
            array $exclude_tags = []
        ): stdClass {
            $current_page = ! is_numeric($current_page) ? 1 : $current_page;
            $current_page = $current_page < 1 ? 1 : $current_page;
            $postId       = Law_Lib_Meta_Data::determinePostId($postId);
            $tag_ids      = [];
            if ($postId) {
                $tags = wp_get_post_tags($postId);
                if ($tags) {
                    foreach ($tags as $individual_tag) {
                        $tag_ids[] = $individual_tag->term_id;
                    }
                }
                unset($tags);
            }
            $category_ids = [];
            $categories   = wp_get_post_categories($postId);
            if ($categories) {
                foreach ($categories as $individual_category) {
                    $category_ids[] = $individual_category;
                }
            }
            unset($categories);
            $exclude_tags = law_lib_component_get_all_tags_as_key_name_sanitize(array_filter($exclude_tags, 'is_numeric'));
            $exclude_categories = law_lib_component_get_all_category_as_key_name_sanitize(array_filter($exclude_categories, 'is_numeric'));

            $order = is_string($order) ? strtolower(trim($order)) : 'DESC';
            $order = in_array($order, ['asc', 'desc', 'rand', 'random'])
                ? $order
                : 'desc';
            $order = strpos($order, 'rand') ? 'rand' : $order;
            $args  = [
                'tag__in'             => $tag_ids,
                'post__not_in'        => $postId ? [$postId] : [],
                'posts_per_page'      => $count, // Number of related posts to display.
                'numberposts'         => $count,
                'current_page'        => $current_page,
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'has_password'        => false,
                'ignore_sticky_posts' => true,
                'suppress_filters'    => true,
                'no_found_rows'       => true,
                'order'               => $order,
                'orderBy'             => 'date',
            ];
            if (!empty($exclude_tags)) {
                $args['tag__not_in'] = $exclude_tags;
            }
            if (!empty($exclude_categories)) {
                $args['category__not_in'] = $exclude_categories;
            }

            if ($orderby && is_string($orderby)) {
                $args['orderby'] = $orderby;
            }
            if ($order === 'rand') {
                $args['orderby'] = $order;
                $s = ['DESC', 'ASC'];
                shuffle($s);
                $args['order'] = $s[0];
            }

            $query = new WP_Query;
            unset($tag_ids);
            $counted = 0;
            $_posts  = $query->query($args);
            $result = new stdClass();
            $result->current_page      = $current_page;
            $result->total_page        = 0;
            $result->found_posts       = $query->found_posts;
            $result->total_found_posts = 0;
            $result->posts = [];
            foreach ($_posts as $k => $post) {
                unset($_posts[$k]);
                $counted++;
                $result->posts[$post->ID] = $post;
                if ($counted >= $count) {
                    break;
                }
            }

            if ( ! empty($category_ids)) {
                $posts_ids   = array_keys($_posts);
                $posts_ids[] = $postId;
                unset($args['tag__in']);
                $args['category__in']   = $category_ids;
                $args['post__not_in']   = $posts_ids;
                $args['posts_per_page'] = ($count - $counted);
                $query                  = new WP_Query;
                $_posts                 = $query->query($args);
                $result->found_posts += $query->found_posts;
                if ($postId && $counted < $count && ($count - $counted) > 0) {
                    foreach ($_posts as $k => $post) {
                        unset($_posts[$k]);
                        $result->posts[$post->ID] = $post;
                        $counted++;
                        if ($counted >= $count) {
                            break;
                        }
                    }
                }
            }

            if (count($result->posts) < $count) {
                if ( ! empty($category_ids)) {
                    $posts_ids   = array_keys($_posts);
                    $posts_ids[] = $postId;
                    unset($args['tag__in'], $args['category__in']);
                    $args['post__not_in']   = $posts_ids;
                    $args['posts_per_page'] = ($count - $counted);
                    $query                  = new WP_Query;
                    $_posts                 = $query->query($args);
                    $result->found_posts += $query->found_posts;
                    if ($postId && $counted < $count && ($count - $counted) > 0) {
                        foreach ($_posts as $k => $post) {
                            unset($_posts[$k]);
                            $result->posts[$post->ID] = $post;
                            $counted++;
                            if ($counted >= $count) {
                                break;
                            }
                        }
                    }
                }
            }

            $query->reset_postdata();
            unset($query, $_posts);
            $result->total_page = ! $result->found_posts ? 0 : ceil($result->found_posts / $count);
            return $result;
        }

        /**
         * @param int $day
         * @param int $num
         * @param int $current_page
         *
         * @return stdClass
         */
        public static function popularPosts(
            int $day = 30,
            int $num = 20,
            int $current_page = 1
        ): stdClass {
            $num = ! is_numeric( $num ) ? 20 : absint( $num );
            if ( $num < 1 ) {
                $num = 1;
            }

            $max          = 100;
            $current_page = ! is_numeric( $current_page ) ? 1 : $current_page;
            $current_page = $current_page < 1 ? 1 : $current_page;
            $max_page     = ceil( $max / $num );
            $current_page = $max_page < $current_page ? $max_page : $current_page;
            $current_page = $current_page < 1 ? 1 : $current_page;
            $num          = $num > $max ? $max : $num;
            $num          = $num < 1 ? 1 : $num;
            $day_default  = apply_filters(
                'tray_digita_util_meta_query_popular_post_days',
                30
            );

            $day_default = ! is_numeric( $day ) ? 30 : absint( $day_default );
            $day         = ! is_numeric( $day ) ? $day_default : $day;
            $day         = $day < 1 ? 1 : $day;
            $cacheExpire = apply_filters(
                'tray_digita_util_meta_query_popular_post_cache_time_default',
                7200
            );
            $cacheExpire = $cacheExpire < 1200 ? 1200 : $cacheExpire;
            $cacheExpire = $cacheExpire > DAY_IN_SECONDS ? DAY_IN_SECONDS : $cacheExpire;
            if ( ! function_exists( 'stats_get_from_restapi' ) ) {
                return Law_Lib_Popular_Views::getInstance()->getViews(
                    $day,
                    $num,
                    $current_page
                );
            }

            $post_view_posts = wp_cache_get(
                "tray_digita_util_meta_query_popular_post_jetpack_{$day}",
                'tray-digita'
            );

            if ( ! is_array( $post_view_posts )
                 || ! isset( $post_view_posts['data'] )
                 || ! $post_view_posts['data'] instanceof stdClass
                 || ! isset( $post_view_posts['data']->summary )
                 || ! is_object( $post_view_posts['data']->summary )
                 || ! isset( $post_view_posts['data']->summary->postviews )
                 || ! is_array( $post_view_posts['data']->summary->postviews )
            ) {
                $post_view_posts = get_site_transient(
                    "tray_digita_util_meta_query_popular_post_jetpack_{$day}"
                );
            }

            if ( empty( $post_view_posts )
                 || ! is_array( $post_view_posts )
                 || ! isset( $post_view_posts['data'] )
                 || ! $post_view_posts['data'] instanceof stdClass
                 || ! isset( $post_view_posts['data']->summary )
                 || ! is_object( $post_view_posts['data']->summary )
                 || ! isset( $post_view_posts['data']->summary->postviews )
                 || ! is_array( $post_view_posts['data']->summary->postviews )
            ) {
                $resource        = 'top-posts?max=100&summarize=1&num=' . $day;
                $post_view_posts = stats_get_from_restapi( [], $resource );
                // use like jetpack
                if ( empty( $post_view_posts ) && class_exists( Client::class ) ) {
                    $endpoint    = jetpack_stats_api_path( $resource );
                    $api_version = '1.1';
                    // Do the dirty work.
                    $response       = Client::wpcom_json_api_request_as_blog( $endpoint, $api_version, [] );
                    $cache_key      = md5( implode( '|', [ $endpoint, $api_version, wp_json_encode( [] ) ] ) );
                    $transient_name = "jetpack_restapi_stats_cache_{$cache_key}";
                    if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
                        // WP_Error.
                        $data = is_wp_error( $response ) ? $response : new WP_Error( 'stats_error' );
                        // WP_Error.
                        $post_view_posts = $data;
                    } else {
                        // string (JSON encoded object).
                        $data = wp_remote_retrieve_body( $response );
                        // object (rare: null on JSON failure).
                        $post_view_posts = json_decode( $data );
                    }
                    // To reduce size in storage: store with time as key, store JSON encoded data (unless error).
                    set_transient( $transient_name, [ time() => $data ], 5 * MINUTE_IN_SECONDS );
                }
                if ( ! is_wp_error( $post_view_posts ) ) {
                    set_site_transient(
                        "tray_digita_util_meta_query_popular_post_jetpack_{$day}",
                        [ 'data' => $post_view_posts ],
                        $cacheExpire
                    );
                    wp_cache_set(
                        "tray_digita_util_meta_query_popular_post_jetpack_{$day}",
                        [ 'data' => $post_view_posts ],
                        'law-lib',
                        $cacheExpire
                    );
                } else {
                    return Law_Lib_Popular_Views::getInstance()->getViews(
                        $day,
                        $num,
                        $current_page
                    );
                }
            } else {
                $post_view_posts = $post_view_posts['data'];
            }
            $std                    = new \stdClass();
            $std->current_page      = $current_page;
            $std->total_page        = 0;
            $std->found_posts       = 0;
            $std->total_found_posts = 0;
            $std->total_views       = 0;
            $std->posts             = [];
            if ( is_object( $post_view_posts ) ) {
                $post_view_posts = $post_view_posts->summary->postviews;
                $post_view_ids   = array_filter( wp_list_pluck( $post_view_posts, 'id' ) );
                $posts_id        = [];
                foreach ( $post_view_posts as $id => $item ) {
                    unset( $post_view_posts[ $id ] );
                    $posts_id[ $item->id ] = $item->views;
                }
                $args = [
                    'post__in'            => $post_view_ids,
                    'posts_per_page'      => $max, // Number of related posts to display.
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'has_password'        => false,
                    'ignore_sticky_posts' => true,
                    //'suppress_filters' => true,
                    //'no_found_rows'    => true,
                ];

                $query                  = new WP_Query;
                $_posts                 = $query->query( $args );
                $std->total_found_posts = $query->found_posts;
                $counted                = 0;
                $offset                 = ( $current_page - 1 ) * $num;
                foreach ( $_posts as $k => $post ) {
                    unset( $_posts[ $k ] );
                    if ( $counted ++ < $offset ) {
                        continue;
                    }
                    $post->views             = $posts_id[ $post->ID ] ?? 0;
                    $std->posts[ $post->ID ] = $post;
                    $std->total_views        += $post->views;
                }
            }

            uasort( $std->posts, function ( $a, $b ) {
                if ( $a->views === $b->views ) {
                    return 0;
                }

                return $a->views > $b->views ? - 1 : 1;
            } );
            $count = 0;
            foreach ( $std->posts as $key => $item ) {
                if ( $count ++ >= $num ) {
                    unset( $std->posts[ $key ] );
                }
            }

            $std->posts      = array_splice( $std->posts, 0, $num );
            $std->total_page = $std->total_found_posts ? ceil( $std->total_found_posts / $num ) : 0;

            return $std;
        }

        public static function archiveName()
        {
            $prefix = '';
            $title = __( 'Archives', 'law-lib' );
            $original_title  = $title;
            if ( is_category() ) {
                $title = __( 'Category', 'law-lib' );
            } elseif ( is_tag() ) {
                $title = __( 'Tag', 'law-lib' );
            } elseif ( is_author() ) {
                $title = __( 'Author', 'law-lib' );
            } elseif ( is_year() ) {
                $title = __( 'Year', 'law-lib' );
            } elseif ( is_month() ) {
                $title = __( 'Month', 'law-lib' );
            } elseif ( is_day() ) {
                $title = __( 'Day', 'law-lib' );
            } elseif ( is_search() ) {
                $title = __( 'Search', 'law-lib' );
            } elseif ( is_tax( 'post_format' ) ) {
                if ( is_tax( 'post_format', 'post-format-aside' ) ) {
                    $title = __( 'Asides', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
                    $title = __( 'Galleries', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
                    $title = __( 'Images', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'law-lib' ) ) {
                    $title = __( 'Videos', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
                    $title = __( 'Quotes', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
                    $title = __( 'Links', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
                    $title = __( 'Statuses', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
                    $title = __( 'Audio', 'law-lib' );
                } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
                    $title = __( 'Chats', 'law-lib' );
                }
            }
            $prefix = apply_filters( 'get_the_archive_title_prefix', $prefix );
            if ( $prefix ) {
                $title = sprintf(
                /* translators: 1: Title prefix. 2: Title. */
                    _x( '%1$s %2$s', 'archive title' , 'law-lib'),
                    $prefix,
                    '<span>' . $title . '</span>'
                );
            }

            return apply_filters( 'get_the_archive_title', $title, $original_title, $prefix );
        }
    }
}

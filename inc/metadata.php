<?php
if ( ! defined('ABSPATH')) :
    return;
endif;

if ( ! function_exists('law_lib_metadata_determine_post_id')) {
    function law_lib_metadata_determine_post_id($post_id)
    {
        $post_id = $post_id ?: get_the_ID();
        $post_id = get_post($post_id);

        return $post_id ? absint($post_id->ID) : 0;
    }
}

if ( ! function_exists('law_lib_metadata_get_primary_category')) {
    /**
     * @param null|int $post_id
     *
     * @return false|WP_Term
     */
    function law_lib_metadata_get_primary_category($post_id = null)
    {
        $post_id = law_lib_metadata_determine_post_id($post_id);
        if ( ! $post_id) {
            return false;
        }

        $term   = 'category';
        $return = [];
        if (class_exists('WPSEO_Primary_Term')) {
            // Show Primary category by Yoast if it is enabled & set
            $wp_seo_primary_term = new WPSEO_Primary_Term($term, $post_id);
            $primary_term        = get_term($wp_seo_primary_term->get_primary_term());
            if ( ! is_wp_error($primary_term)) {
                $return['primary_category'] = $primary_term;
            }
        } else {
            $rank_math_primary_term = get_post_meta($post_id, 'rank_math_primary_category', true);
            if (is_numeric($rank_math_primary_term)) {
                $primary_term = get_term($rank_math_primary_term);
                if ( ! is_wp_error($primary_term)) {
                    $return['primary_category'] = $primary_term;
                }
            }
        }

        if (empty($return['primary_category'])) {
            $categories_list = get_the_terms($post_id, $term);
            if ( ! empty($categories_list)) {
                $return['primary_category'] = $categories_list[0];
            }
        }

        $return = $return['primary_category'] ?? false;
        if ($return instanceof WP_Term) {
            return $return;
        }

        return false;
    }
}

if ( ! function_exists('law_lib_metadata_related_posts')) {
    function law_lib_metadata_related_posts(
        $postId = null,
        int $count = 10,
        string $orderby = null,
        string $order = 'desc',
        int $current_page = 1
    ): array {
        $current_page = ! is_numeric($current_page) ? 1 : $current_page;
        $current_page = $current_page < 1 ? 1 : $current_page;
        $posts        = [];
        $postId       = law_lib_metadata_determine_post_id($postId);
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
        $order = ! is_string($order)
            ? 'DESC'
            : (strtolower(trim($order)) === 'asc' ? 'ASC' : 'DESC');
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
        ];

        if ($orderby && is_string($orderby)) {
            $args['orderby'] = $orderby;
        }

        $query = new WP_Query;
        unset($tag_ids);
        $counted = 0;
        $_posts  = $query->query($args);
        $found   = $query->found_posts;
        foreach ($_posts as $k => $post) {
            unset($_posts[$k]);
            $counted++;
            $posts[$post->ID] = $post;
            if ($counted >= $count) {
                break;
            }
        }
        unset($query);

        if ( ! empty($category_ids)) {
            $posts_ids   = array_keys($posts);
            $posts_ids[] = $postId;
            unset($args['tag__in']);
            $args['category__in']   = $category_ids;
            $args['post__not_in']   = $posts_ids;
            $args['posts_per_page'] = ($count - $counted);
            $query                  = new WP_Query;
            $_posts                 = $query->query($args);
            $found                  += $query->found_posts;
            if ($postId && $counted < $count && ($count - $counted) > 0) {
                foreach ($_posts as $k => $post) {
                    unset($_posts[$k]);
                    $posts[$post->ID] = $post;
                    $counted++;
                    if ($counted >= $count) {
                        break;
                    }
                }
            }
        }
        unset($query, $_posts);
        $total_page = ! $found ? 0 : ceil($found / $count);

        return [
            'total'        => $found,
            'current_page' => $current_page,
            'total_page'   => $total_page,
            'posts'        => $posts,
        ];
    }
}

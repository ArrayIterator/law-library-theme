<?php
if (!class_exists('Law_Lib_Meta_Data')) {
    class Law_Lib_Meta_Data
    {
        /**
         * @var null|array<int, string>
         */
        private static $tags = null;

        /**
         * @var null|array<int, string>
         */
        private static $categories = null;

        /**
         * Get tags term_id as key
         *
         * @return array<int, string>
         */
        public static function tagList(): array
        {
            if (is_array(self::$tags)) {
                return self::$tags;
            }
            self::$tags = [];
            /**
             * @var WP_Term $tag
             */
            foreach (get_tags() as $tag) {
                if ( ! $tag->name) {
                    continue;
                }
                self::$tags[$tag->term_id] = $tag->name;
            }

            return self::$tags;
        }

        /**
         * Get categories term_id as key
         *
         * @return array<int, string>
         */
        public static function categoryList(): array
        {
            if (is_array(self::$categories)) {
                return self::$categories;
            }
            self::$categories = [];
            /**
             * @var WP_Term $cat
             */
            foreach (get_categories() as $cat) {
                if ( ! $cat->name) {
                    continue;
                }
                self::$categories[$cat->term_id] = $cat->name;
            }

            return self::$categories;
        }

        /**
         * @param null $post_id
         *
         * @return int
         */
        public static function determinePostId($post_id = null): int
        {
            $post_id = is_numeric($post_id) && $post_id < 1 ? 0 : ($post_id ?: get_the_ID());
            $post_id = get_post($post_id);

            return $post_id ? absint($post_id->ID) : 0;
        }

        /**
         * @param null|WP_Post|int $post_id
         *
         * @return false|WP_Term
         */
        public static function primaryCategory(
            $post_id = null
        ) {
            $post_id = self::determinePostId($post_id);
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
}

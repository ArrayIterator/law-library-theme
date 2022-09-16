<?php
if (!defined('ABSPATH')) :
    return;
endif;

if ( ! function_exists( 'law_lib_formatting_disable_wp_texturize' ) ) {
    /**
     * We Hate Smart Punctuation
     */
    function law_lib_formatting_disable_wp_texturize()
    {
        $opt = apply_filters('law_lib_formatting_wp_texturize_mode', 'all');
        if ($opt === 'content') {
            $content = [
                'the_content',
                'the_excerpt',
                'the_post_thumbnail_caption',
                'widget_text_content',
                'comment_text',
                'comment_author',
            ];
            array_map( function ( $a ) {
                remove_filter( $a, 'wptexturize' );
            }, $content );

            return;
        }
        if ( $opt === 'title' ) {
            $title = [
                'the_title',
                'wp_title',
                'document_title',
                'widget_title',
                'single_post_title',
                'single_tag_title',
                'single_cat_title',
                'nav_menu_attr_title',
            ];
            array_map( function ( $a ) {
                remove_filter( $a, 'wptexturize' );
            }, $title );

            return;
        }

        if ( $opt !== 'all' ) {
            return;
        }

        /**
         * TOTALLY DISABLE WP TEXTURIZE
         */
        add_filter( 'run_wptexturize', '__return_false' );

        remove_filter( 'the_title', 'wptexturize' );
        remove_filter( 'the_content', 'wptexturize' );
        remove_filter( 'the_excerpt', 'wptexturize' );
        remove_filter( 'the_post_thumbnail_caption', 'wptexturize' );
        remove_filter( 'comment_text', 'wptexturize' );
        remove_filter( 'list_cats', 'wptexturize' );
        remove_filter( 'widget_text_content', 'wptexturize' );
        remove_filter( 'term_description', 'wptexturize' );
        remove_filter( 'get_the_post_type_description', 'wptexturize' );
        remove_filter( 'single_post_title', 'wptexturize' );
        remove_filter( 'single_cat_title', 'wptexturize' );
        remove_filter( 'single_tag_title', 'wptexturize' );
        remove_filter( 'single_month_title', 'wptexturize' );
        remove_filter( 'nav_menu_attr_title', 'wptexturize' );
        remove_filter( 'nav_menu_description', 'wptexturize' );
        remove_filter( 'comment_author', 'wptexturize' );
        remove_filter( 'term_name', 'wptexturize' );
        remove_filter( 'link_name', 'wptexturize' );
        remove_filter( 'link_description', 'wptexturize' );
        remove_filter( 'link_notes', 'wptexturize' );
        remove_filter( 'bloginfo', 'wptexturize' );
        remove_filter( 'wp_title', 'wptexturize' );
        remove_filter( 'document_title', 'wptexturize' );
        remove_filter( 'widget_title', 'wptexturize' );
    }
}

add_action('init', 'law_lib_formatting_disable_wp_texturize');

if (!function_exists('law_lib_formatting_wp_texturize_mode_hook')) {
    function law_lib_formatting_wp_texturize_mode_hook($value)
    {
        $option = law_lib_component_option('homepage', 'wp_texturize', 'all');
        return !in_array($option, ['all', 'content', 'title']) ? 'all' : $option;
    }
}
add_filter('law_lib_formatting_wp_texturize_mode', 'law_lib_formatting_wp_texturize_mode_hook');

if (!function_exists('law_lib_formatting_the_excerpt')) {
    function law_lib_formatting_the_excerpt($excerpt)
    {
        $is_home = law_lib_logic_is_homepage() && empty($wp_query->has_front_page_data);
        $named = $is_home
            ? 'homepage'
            : 'archive';
        $default_length = $is_home ? null : 50;
        $length = law_lib_component_option($named, 'excerpt_length', $default_length);
        $after = law_lib_component_option($named, 'excerpt_after');
        $excerpt = strip_tags($excerpt);
        $old_excerpt = $excerpt;
        if (is_numeric($length)) {
            $excerpt = substr($excerpt, 0, $length);
        }
        if ($old_excerpt !== $excerpt) {
            $excerpt = apply_filters(
                'law_lib_formatting_the_excerpt_after',
                sprintf(
                    '%s<span class="excerpt-after">%s</span>',
                    $excerpt,
                    is_string($after) ? $after : ''
                ),
                $excerpt,
                $old_excerpt,
                $length
            );
        }
        return $excerpt;
    }
}
add_filter('the_excerpt', 'law_lib_formatting_the_excerpt');

// returning empty string more
add_filter('excerpt_more', '__return_empty_string');

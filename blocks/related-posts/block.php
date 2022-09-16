<?php
if (!defined('ABSPATH')) :
    return;
endif;
if (!function_exists('law_lib_blocks_related_posts_render_callback')) {
    function law_lib_blocks_related_posts_render_callback($block_attributes, $content)
    {
        $num = $block_attributes['total']?? 5;
        $num = !is_numeric($num) ? 3 : absint($num);
        $num = $num < 2 ? 2 : $num;
        $num = $num > 50 ? 50 : $num;
        $show_thumbnail = ($block_attributes['show_thumbnail']?? 'yes') !== 'no';
        $show_date      = ($block_attributes['show_date']?? 'yes') !== 'no';
        $order = ($block_attributes['sort']?? 'desc');
        $orderBy = ($block_attributes['order']?? 'order');
        $show_category = ($block_attributes['show_category']?? 'yes') !== 'no';
        $categories = ($block_attributes['exclude_categories']??[]);
        $categories = !is_array($categories) ? [] : $categories;
        $categories = array_filter($categories, 'is_numeric');
        $categories = law_lib_component_get_all_category_as_key_name_sanitize($categories);
        $related_posts = Law_Lib_Meta_Query::relatedPosts(
            null,
            $num,
            $orderBy,
            $order,
            1,
            $categories
        );
        $className = $block_attributes['className']??'';
        $class = '';
        if ($className && is_string($className)) {
            $className = explode(' ', $className);
            $className = array_map('sanitize_html_class', $className);
            $className = array_filter($className);
            $class = implode(' ', $className);
            $class = $class ? " $class" : '';
        }
        $html = "<div class=\"law-lib-related-posts law-lib-block-wrapper{$class}\">";
        if (!empty($related_posts)) {
            $html .= '<ol class="law-lib-block-list">';
        }
        $date_format = get_option('date_format');
        $aria_title = esc_attr_x('Related Post', 'blocks', 'law-lib');
        $aria_image = esc_attr_x('Related Post Thumbnail', 'blocks', 'law-lib');
        $aria_category = esc_attr_x('Related Post Category', 'blocks', 'law-lib');
        /**
         * @var WP_Post $post
         */
        foreach ($related_posts->posts as $post) {
            $permalink = esc_url(get_permalink($post));
            $attr_title = esc_attr($post->post_title);
            $ent_title = esc_html($post->post_title);
            $html.= '<li class="law-lib-block-item">';
            if ($show_thumbnail) {
                $thumb = Law_Lib_Image::thumbnailImageAttachment(
                    $post,
                    Law_Lib_Image::POST_SQUARE_MINI
                );
                if ($thumb) {
                    $html .= '<div class="law-lib-block-thumbnail">';
                    $html .= "<a href='$permalink' title='$attr_title' aria-label='$aria_image' class='law-lib-block-link law-lib-block-thumbnail-link'>";
                    $html .= $thumb['html'];
                    $html .= "</a>";
                    $html .= '</div>';
                }
            }
            $html .= '<div class="law-lib-block-content">';
            $html .= '<div class="law-lib-block-title">';
            $html .= "<a href='$permalink' title='$attr_title' aria-label='$aria_title' class='law-lib-block-link law-lib-block-title-link'>$ent_title</a>";
            $html .= '</div>';
            if ($show_date || $show_category) {
                $html   .= '<div class="law-lib-block-meta">';
                if ($show_category) {
                    $term = Law_Lib_Meta_Data::primaryCategory($post);
                    if ($term) {
                        $cat_link = esc_url(get_category_link($term->term_id));
                        $attr_cat = esc_attr($term->name);
                        $html .= '<div class="law-lib-block-meta-item block-category-item">';
                        $html .= "<a href='$cat_link' title='$attr_cat' aria-label='$aria_category' class='law-lib-block-link law-lib-block-category-link'>";
                        $html .= esc_html($term->name);
                        $html .= "</a>";
                        $html .= "</div>";
                    }
                }
                if ($show_date) {
                    $_local = get_post_datetime($post->ID);
                    $html   .= '<div class="law-lib-block-meta-item block-date-item">';
                    $html   .= sprintf(
                        '<time datetime="%1$s" class="published-date">%2$s</time>',
                        esc_attr(get_the_date('c', $post->ID)),
                        esc_html(wp_date($date_format, $_local->getTimestamp(), $_local->getTimezone()))
                    );
                    $html .= "</div>";
                }

                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</li>';
        }
        if (!empty($related_posts)) {
            $html .= '</ol>';
        }
        $html .= '</div>';
        return $html;
    }
}
if (!function_exists('law_lib_blocks_related_posts')) {
    function law_lib_blocks_related_posts()
    {
        // automatically load dependencies and version
        $baseName = basename(dirname(__DIR__)) . '/' . basename(__DIR__);
        wp_register_script(
            'law-lib-blocks-related-posts',
            get_theme_file_uri($baseName . '/block.js'),
            ['wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-server-side-render', 'wp-block-library']
        );
        register_block_type('law-lib-blocks/related-posts', [
            'api_version'     => 2,
            'editor_script'   => 'law-lib-blocks-related-posts',
            'render_callback' => 'law_lib_blocks_related_posts_render_callback',
            'attributes' => [
                'total' => [
                    'default' => 5,
                    'type' => 'number'
                ],
                'show_thumbnail' => [
                    'default' => 'yes',
                    'type' => 'string'
                ],
                'show_date' => [
                    'default' => 'yes',
                    'type' => 'string'
                ],
                'show_category' => [
                    'default' => 'yes',
                    'type' => 'string'
                ],
                'order' => [
                    'default' => 'date',
                    'type' => 'string'
                ],
                'sort' => [
                    'default' => 'desc',
                    'type' => 'string'
                ],
                'exclude_categories' => [
                    'default' => [],
                    'type' => 'array'
                ]
            ]
        ]);
    }
}
add_action( 'init', 'law_lib_blocks_related_posts' );

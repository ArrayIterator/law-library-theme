<?php
if (!defined('ABSPATH')) :
    return;
endif;
if (!function_exists('law_lib_blocks_amp_slider_render_callback')) {
    function law_lib_blocks_amp_slider_render_callback($block_attributes, $content)
    {
        $num = $block_attributes['total']?? 5;
        $num = !is_numeric($num) ? 3 : absint($num);
        $num = $num < 2 ? 2 : $num;
        $num = $num > 20 ? 20 : $num;
        $show_thumbnail = ($block_attributes['show_thumbnail']?? 'yes') !== 'no';
        $show_date      = ($block_attributes['show_date']?? 'yes') !== 'no';
        $show_title      = ($block_attributes['show_title']?? 'yes') !== 'no';
        $order = ($block_attributes['sort']?? 'desc');
        $orderBy = ($block_attributes['order']?? 'order');
        $show_category = ($block_attributes['show_category']?? 'yes') !== 'no';
        $categories = ($block_attributes['categories']??[]);
        $categories = !is_array($categories) ? [] : $categories;
        $categories = array_filter($categories, 'is_numeric');
        $categories = law_lib_component_get_all_category_as_key_name_sanitize($categories);
        $tags = ($block_attributes['tags']??[]);
        $tags = !is_array($tags) ? [] : $tags;
        $tags = array_filter($tags, 'is_numeric');
        $tags = law_lib_component_get_all_tags_as_key_name_sanitize($tags);
        $order = is_string($order) ? strtolower(trim($order)) : 'DESC';
        $order = in_array($order, ['asc', 'desc', 'rand', 'random'])
            ? $order
            : 'desc';
        $order = strpos($order, 'rand') ? 'rand' : $order;
        $args  = [
            'posts_per_page'      => $num + 10, // Number of related posts to display.
            'numberposts'         => $num + 10,
            'current_page'        => 1,
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'has_password'        => false,
            'ignore_sticky_posts' => true,
            'suppress_filters'    => true,
            'no_found_rows'       => true,
            'order'               => $order,
            'orderBy'             => 'date',
        ];
        if (!empty($categories)) {
            $args['category__in'] = $categories;
        }
        if (!empty($tags)) {
            $tags['tag__in'] = $tags;
        }

        if ($orderBy && is_string($orderBy)) {
            $args['orderby'] = $orderBy;
        }
        if ($order === 'rand') {
            $args['orderby'] = $order;
            $s = ['DESC', 'ASC'];
            shuffle($s);
            $args['order'] = $s[0];
        }

        static $count = 0;
        $_carousel = (new Law_Lib_Amp_Carousel())->create(
            'amp-carousel-'.$count ++,
            //  Carousel::LAYOUT_FILL,
            Law_Lib_Amp_Carousel::LAYOUT_RESPONSIVE,
            Law_Lib_Amp_Carousel::TYPE_SLIDES,
            [
                'loop' => true
            ]
        );

        $_carousel->setPostThumbnailSize(Law_Lib_Image::POST_THUMBNAIL_WIDEST);
        $_carousel->setPostPreviewThumbnailSize(Law_Lib_Image::POST_THUMBNAIL_MINI);
        $_carousel->setEnablePreview($show_thumbnail);
        $date_format = get_option('date_format');
        $aria_title = esc_attr_x('AMP Post', 'blocks', 'law-lib');
        $aria_image = esc_attr_x('AMP Post Thumbnail', 'blocks', 'law-lib');
        $aria_category = esc_attr_x('AMP Post Category', 'blocks', 'law-lib');

        $counted = 0;
        foreach (get_posts($args) as $post) {
            $captions = null;
                $permalink = esc_url(get_permalink($post));
            if ($show_date || $show_category || $show_title) {
                $captions = '<div class="law-lib-block-caption">';
                if ($show_title) {
                    $attr_title = esc_attr($post->post_title);
                    $ent_title = esc_html($post->post_title);
                    $captions .= '<div class="law-lib-block-title">';
                    $captions .= "<a href='$permalink' title='$attr_title' aria-label='$aria_title' class='law-lib-block-link law-lib-block-thumbnail-link' tabindex='-1'>";
                    $captions .= $ent_title;
                    $captions .= '</a>';
                    $captions .= '</div>';
                }
                $captions .= '<div class="law-lib-block-meta">';
                if ($show_category) {
                    $term = Law_Lib_Meta_Data::primaryCategory($post);
                    if ($term) {
                        $cat_link = esc_url(get_category_link($term->term_id));
                        $attr_cat = esc_attr($term->name);
                        $captions .= '<div class="law-lib-block-meta-item block-category-item">';
                        $captions .= "<a href='$cat_link' title='$attr_cat' aria-label='$aria_category' class='law-lib-block-link law-lib-block-category-link' tabindex='-1'>";
                        $captions .= esc_html($term->name);
                        $captions .= "</a>";
                        $captions .= "</div>";
                    }
                }
                if ($show_date) {
                    $_local = get_post_datetime($post->ID);
                    $captions   .= '<div class="law-lib-block-meta-item block-date-item">';
                    $captions   .= sprintf(
                        '<time datetime="%1$s" class="published-date">%2$s</time>',
                        esc_attr(get_the_date('c', $post->ID)),
                        esc_html(wp_date($date_format, $_local->getTimestamp(), $_local->getTimezone()))
                    );
                    $captions .= "</div>";
                }
                $captions .= '</div>';
                $captions .= '</div>';
            }

            $inserted = $_carousel->addFromPost(
                $post,
                null,
                $captions,
                sprintf('<a href="%s" class="carousel-image-permalink carousel-image" aria-label="%s" tabindex="-1">', $permalink, $aria_image),
                '</a>'
            );
            if ($inserted) {
                $counted++;
            }
            if ($counted >= $num) {
                break;
            }
        }

        $additional_class = $show_thumbnail ? 'amp-slider-with-thumbnail' : '';
        $html = '<div class="carousel-content law-lib-amp-slider law-lib-block-wrapper '.$additional_class.'">';
        $html .= $_carousel->toHtml();
        $html .= '</div>';
        unset($_carousel);
        return $html;
    }
}
if (!function_exists('law_lib_blocks_amp_slider')) {
    function law_lib_blocks_amp_slider()
    {
        // automatically load dependencies and version
        $baseName = basename(dirname(__DIR__)) . '/' . basename(__DIR__);
        wp_register_script(
            'law-lib-blocks-amp-slider',
            get_theme_file_uri($baseName . '/block.js'),
            ['wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-server-side-render', 'wp-block-library']
        );
        register_block_type('law-lib-blocks/amp-slider', [
            'api_version'     => 2,
            'editor_script'   => 'law-lib-blocks-amp-slider',
            'render_callback' => 'law_lib_blocks_amp_slider_render_callback',
            'attributes' => [
                'total' => [
                    'default' => 5,
                    'type' => 'number'
                ],
                'show_thumbnail' => [
                    'default' => 'yes',
                    'type' => 'string'
                ],
                'show_title' => [
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
                'categories' => [
                    'default' => [],
                    'type' => 'array'
                ],
                'tags' => [
                    'default' => [],
                    'type' => 'array'
                ]
            ]
        ]);
    }
}
add_action( 'init', 'law_lib_blocks_amp_slider' );

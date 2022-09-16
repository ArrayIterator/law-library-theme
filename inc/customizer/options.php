<?php
if (!defined('ABSPATH')) :
    return [];
endif;

return [
    'homepage' => [
        'args' => [
            'title' => __('Homepage', 'law-lib'),
        ],
        'settings' => [
            'show_blogs' => [
                'label'       => __( 'Show Blog Posts', 'law-lib' ),
                'description' => __( 'Show blog posts area below homepage section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'blog_filtering' => [
                'label'       => __( 'Enable Blog Filtering', 'law-lib' ),
                'description' => __( 'Filtering blog posts. Disable it when you want show normal blog posts. (below setting will be ignored).', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_thumbnail' => [
                'label'       => __( 'Show Thumbnail', 'law-lib' ),
                'description' => __( 'Show blog posts thumbnail.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_category' => [
                'label'       => __( 'Show Category', 'law-lib' ),
                'description' => __( 'Show blog posts category.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'blog_categories' => [
                'label'       => __( 'Blog Categories', 'law-lib' ),
                'description' => __( 'Include only certain categories.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'multiselect',
                'choices'     => law_lib_component_get_all_category_as_key_name(),
                'sanitize_callback' => 'law_lib_component_get_all_category_as_key_name_sanitize',
            ],
            'show_excerpt' => [
                'label'       => __( 'Show Excerpt', 'law-lib' ),
                'description' => __( 'Show blog posts excerpt.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'excerpt_length' => [
                'label'       => __( 'Blog Excerpt Length', 'law-lib' ),
                'description' => __( 'Excerpt length for blog loop.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'number',
                'default'     => '50',
                'sanitize_callback' => function($e) {
                    if (!is_numeric($e)) {
                        return 50;
                    }
                    $e = absint($e);
                    $e = $e < 1 ? 0 : $e;
                    return $e > 1000 ? 1000 : $e;
                }
            ],
            'excerpt_after' => [
                'label'       => __( 'Text After Excerpt', 'law-lib' ),
                'description' => __( 'Text inserted after excerpt.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'text',
                'default'     => '...',
            ],
            'show_date' => [
                'label'       => __( 'Show Date', 'law-lib' ),
                'description' => __( 'Show blog posts date.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_author' => [
                'label'       => __( 'Show Author', 'law-lib' ),
                'description' => __( 'Show blog posts author.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_blog_post_count' => [
                'label'       => __( 'Initial Post Count', 'law-lib' ),
                'description' => __( 'Show blog initial list posts count on blog section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'number',
                'default'     => 10,
                'choices'     => (function() {
                    $range = range(3, 50);
                    $keys = array_flip($range);
                    foreach ($keys as $key => $item) {
                        $keys[$key] = $range[$item];
                    }
                    return $keys;
                })(),
            ],
            'load_more_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Load More', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
            'loading_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Loading...', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
            'per_page' => [
                'label'       => __( 'Ajax List Item', 'law-lib' ),
                'description' => __( 'How much list to show per ajax request.', 'law-lib' ),
                'priority'    => 10,
                'default'     => 5,
                'transport'   => 'refresh',
                'type'        => 'select',
                'choices'     => (function() {
                    $range = range(2, 50);
                    $keys = array_flip($range);
                    foreach ($keys as $key => $item) {
                        $keys[$key] = $range[$item];
                    }
                    return $keys;
                })(),
            ],
        ]
    ],
    'global' => [
        'args' => [
            'title' => __('Global Settings', 'law-lib'),
        ],
        'settings' => [
            'wp_texturize' => [
                'label'       => __( 'Disable Texturize', 'law-lib' ),
                'description' => __( 'Show breadcrumb on post section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'all',
                'choices'     => [
                    'all' => __('All', 'law-lib'),
                    'title' => __('Title Only', 'law-lib'),
                    'content' => __('Content Only', 'law-lib'),
                ],
            ],
            'show_breadcrumb_post' => [
                'label'       => __( 'Show Breadcrumb Post', 'law-lib' ),
                'description' => __( 'Show breadcrumb on post section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_breadcrumb_page' => [
                'label'       => __( 'Show Breadcrumb Page', 'law-lib' ),
                'description' => __( 'Show breadcrumb on page section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_breadcrumb_archive' => [
                'label'       => __( 'Show Breadcrumb Archive & Search', 'law-lib' ),
                'description' => __( 'Show breadcrumb on archive section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_archive_title' => [
                'label'       => __( 'Show Archive Title', 'law-lib' ),
                'description' => __( 'Show description on archive title.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_archive_description' => [
                'label'       => __( 'Show Archive & Search Description', 'law-lib' ),
                'description' => __( 'Show description on archive section.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'copyright_text' => [
                'label'       => __( 'Copyright', 'law-lib' ),
                'description' => sprintf(
                    '%s<br>%s',
                    __( 'Footer Copyright (HTML Support)', 'law-lib' ),
                    sprintf(
                        '{year} %1$s, {url} %2$s, {copy} %3$s, {name} %4$s',
                        __( 'For Year', 'law-lib' ),
                        __( 'For Home URL', 'law-lib' ),
                        sprintf( __( 'For %s', 'law-lib' ), '&copy;' ),
                        __( 'For Site Name', 'law-lib' )
                    )
                ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'textarea',
                'default'     => '<div class="text-center text-small">{copy} {year} <a href="{url}" class="home-url" rel="home">{name}</a>. All Right Reserved.</div>',
            ],
        ]
    ],
    'archive' => [
        'args' => [
            'title' => __('Archive Settings', 'law-lib'),
        ],
        'settings' => [
            'show_thumbnail' => [
                'label'       => __( 'Show Thumbnail', 'law-lib' ),
                'description' => __( 'Show blog posts thumbnail.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_category' => [
                'label'       => __( 'Show Category', 'law-lib' ),
                'description' => __( 'Show blog posts category.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_excerpt' => [
                'label'       => __( 'Show Excerpt', 'law-lib' ),
                'description' => __( 'Show blog posts excerpt.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'excerpt_length' => [
                'label'       => __( 'Archive Excerpt Length', 'law-lib' ),
                'description' => __( 'Excerpt length for content loop.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'number',
                'default'     => '50',
            ],
            'excerpt_after' => [
                'label'       => __( 'Text After Excerpt', 'law-lib' ),
                'description' => __( 'Text inserted after excerpt.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'text',
                'default'     => '...',
            ],
            'show_date' => [
                'label'       => __( 'Show Date', 'law-lib' ),
                'description' => __( 'Show blog posts date.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_author' => [
                'label'       => __( 'Show Author', 'law-lib' ),
                'description' => __( 'Show blog posts author.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'ajax_pagination' => [
                'label'       => __( 'Ajax Load More', 'law-lib' ),
                'description' => __( 'Show as ajax load more pagination.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'categories_exclude' => [
                'label'       => __( 'Exclude Categories', 'law-lib' ),
                'description' => __( 'Exclude certain categories.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'multiselect',
                'choices'     => law_lib_component_get_all_category_as_key_name(),
                'sanitize_callback' => 'law_lib_component_get_all_category_as_key_name_sanitize',
            ],
            'load_more_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Load More', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
            'loading_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Loading...', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
        ]
    ],
    'post' => [
        'args' => [
            'title' => __('Blog Post Settings', 'law-lib'),
        ],
        'settings' => [
            'show_thumbnail' => [
                'label'       => __( 'Show Thumbnail', 'law-lib' ),
                'description' => __( 'Show blog post thumbnail on post.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'default',
                'choices'     => [
                    'default' => __('Default', 'law-lib'),
                    'force_enable' => __('Force Enable', 'law-lib'),
                    'force_disable'  => __('Force Disable', 'law-lib'),
                ],
            ],
            'show_meta' => [
                'label'       => __( 'Show Header Meta', 'law-lib' ),
                'description' => __( 'Show blog post meta.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_category' => [
                'label'       => __( 'Show Category', 'law-lib' ),
                'description' => __( 'Show blog post category.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_date' => [
                'label'       => __( 'Show Date', 'law-lib' ),
                'description' => __( 'Show blog post date.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_author' => [
                'label'       => __( 'Show Author', 'law-lib' ),
                'description' => __( 'Show blog post author.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_tags' => [
                'label'       => __( 'Show Tags', 'law-lib' ),
                'description' => __( 'Show blog post tag.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_author_bio' => [
                'label'       => __( 'Show Author Bio', 'law-lib' ),
                'description' => __( 'Show blog post author bio.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
        ]
    ],
    'search' => [
        'args' => [
            'title' => __('Search Query', 'law-lib'),
        ],
        'settings' => [
            'exclude_page' => [
                'label'       => __( 'Exclude Page From Search', 'law-lib' ),
                'description' => __( 'Enable post type page from search result.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'categories_exclude_search' => [
                'label'       => __( 'Search Exclude Categories', 'law-lib' ),
                'description' => __( 'Exclude certain categories to prevent show on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'multiselect',
                'choices'     => law_lib_component_get_all_category_as_key_name(),
                'sanitize_callback' => 'law_lib_component_get_all_category_as_key_name_sanitize',
            ],
            'categories_exclude' => [
                'label'       => __( 'Mobile Ajax Exclude Categories', 'law-lib' ),
                'description' => __( 'Exclude certain categories to prevent show on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'multiselect',
                'choices'     => law_lib_component_get_all_category_as_key_name(),
                'sanitize_callback' => 'law_lib_component_get_all_category_as_key_name_sanitize',
            ],
            'per_page_first' => [
                'label'       => __( 'Number First Posts', 'law-lib' ),
                'description' => __( 'How much list to show on first request.', 'law-lib' ),
                'priority'    => 10,
                'default'     => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'choices'     => (function() {
                    $range = range(3, 50);
                    $keys = array_flip($range);
                    foreach ($keys as $key => $item) {
                        $keys[$key] = $range[$item];
                    }
                    return $keys;
                })(),
            ],
            'per_page' => [
                'label'       => __( 'Number To Show Posts', 'law-lib' ),
                'description' => __( 'How much list to show per ajax request.', 'law-lib' ),
                'priority'    => 10,
                'default'     => 5,
                'transport'   => 'refresh',
                'type'        => 'select',
                'choices'     => (function() {
                    $range = range(2, 50);
                    $keys = array_flip($range);
                    foreach ($keys as $key => $item) {
                        $keys[$key] = $range[$item];
                    }
                    return $keys;
                })(),
            ],
            'show_date' => [
                'label'       => __( 'Show Date', 'law-lib' ),
                'description' => __( 'Show date on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'show_thumbnail' => [
                'label'       => __( 'Show Thumbnail', 'law-lib' ),
                'description' => __( 'Show thumbnail on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'load_more_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Load More', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
            'loading_text' => [
                'label'       => __( 'Load More Text', 'law-lib' ),
                'description' => __( 'Load more text on ajax search.', 'law-lib' ),
                'priority'    => 10,
                'default'     => __('Loading...', 'law-lib'),
                'transport'   => 'refresh',
                'type'        => 'text',
            ],
            'search_microdata' => [
                'label'       => __( 'Enable Search Microdata', 'law-lib' ),
                'description' => __( 'Enable microdata search on single page. If rank math exists, will handle by rank math.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
        ]
    ],
    'layout' => [
        'args' => [
            'title' => __('Layout & Colors', 'law-lib'),
        ],
        'settings' => [
            'link-color'     => [
                'label'       => __( 'Link', 'law-lib' ),
                'description' => __( 'Global link color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'link-hover-color'     => [
                'label'       => __( 'Link Hover', 'law-lib' ),
                'description' => __( 'Global link hover color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'link-focus-color'     => [
                'label'       => __( 'Link Focus', 'law-lib' ),
                'description' => __( 'Global link focus color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-color'     => [
                'label'       => __( 'Primary Color', 'law-lib' ),
                'description' => __( 'Theme primary color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-darker-color'     => [
                'label'       => __( 'Primary Darker Color', 'law-lib' ),
                'description' => __( 'Theme primary darker color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-light-color'     => [
                'label'       => __( 'Primary Light Color', 'law-lib' ),
                'description' => __( 'Theme primary light color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-text-color'     => [
                'label'       => __( 'Primary Text', 'law-lib' ),
                'description' => __( 'Theme primary text color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-text-hover-color'     => [
                'label'       => __( 'Primary Text Hover', 'law-lib' ),
                'description' => __( 'Theme primary text color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-link-color'     => [
                'label'       => __( 'Primary Link', 'law-lib' ),
                'description' => __( 'Theme primary link color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-link-hover-color'     => [
                'label'       => __( 'Primary Link Hover', 'law-lib' ),
                'description' => __( 'Theme primary link hover color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'primary-link-focus-color'     => [
                'label'       => __( 'Primary Link Focus Color', 'law-lib' ),
                'description' => __( 'Theme primary link focus color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'meta-link-color'     => [
                'label'       => __( 'Meta Link Color', 'law-lib' ),
                'description' => __( 'Theme meta link color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'meta-link-hover-color'     => [
                'label'       => __( 'Meta Link Hover Color', 'law-lib' ),
                'description' => __( 'Theme meta link hover color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            //                'footer-link-hover-color',
            //                'footer-link-focus-color',
            'footer-background-color'     => [
                'label'       => __( 'Footer Background', 'law-lib' ),
                'description' => __( 'Footer background color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'footer-text-color'     => [
                'label'       => __( 'Footer Text', 'law-lib' ),
                'description' => __( 'Theme footer text color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'footer-text-hover-color'     => [
                'label'       => __( 'Footer Text Hover', 'law-lib' ),
                'description' => __( 'Theme footer text hover color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'footer-link-color'     => [
                'label'       => __( 'Footer Link', 'law-lib' ),
                'description' => __( 'Theme footer link color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'footer-link-hover-color'     => [
                'label'       => __( 'Footer Link Hover', 'law-lib' ),
                'description' => __( 'Theme footer link hover color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
            'footer-link-focus-color'     => [
                'label'       => __( 'Footer Link Focus Color', 'law-lib' ),
                'description' => __( 'Theme footer link focus color.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => WP_Customize_Color_Control::class,
                'default'     => '',
            ],
        ]
    ],
    'misc' => [
        'args' => [
            'title' => __('Miscellaneous', 'law-lib')
        ],
        // sitemap/remove_credit
        'settings' => [
            'disable_sidebar_text' => [
                'label'       => __( 'Disable Sidebar Text', 'law-lib' ),
                'description' => __( 'Disable text on menu sidebar.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'disable_tracking' => [
                'label'       => __( 'Disable Tracking', 'law-lib' ),
                'description' => __( 'Disable popular post tracking.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'rank_amp_generator' => [
                'label'       => __( 'Remove AMP & Wordpress Generator', 'law-lib' ),
                'description' => __( 'Remove AMP & WordPress meta generator.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'rank_math_sitemap' => [
                'label'       => __( 'Rank Math Disable Credit', 'law-lib' ),
                'description' => __( 'Disable rank math credits.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'postMessage',
                'type'        => 'select',
                'default'     => 'yes',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'enable_comment' => [
                'label'       => __( 'Enable Comment', 'law-lib' ),
                'description' => __( 'Enable post or page comment.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'default',
                'choices'     => [
                    'default' => __('Default', 'law-lib'),
                    'facebook' => __('Default Use Facebook', 'law-lib'),
                    'post_facebook' => __('Page Use Facebook', 'law-lib'),
                    'page_facebook' => __('Page Only Use Facebook', 'law-lib'),
                    'disable'  => __('Disable All Comments', 'law-lib'),
                ],
            ],
            'facebook_comment_app_id' => [
                'label'       => __( 'Facebook Comment APP ID', 'law-lib' ),
                'description' => __( 'Facebook APP ID for comments.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'text',
                'default'     => '',
            ],
            'facebook_insert_to_meta' => [
                'label'       => __( 'Facebook APP ID Insertion', 'law-lib' ),
                'description' => __( 'Insert Facebook APP ID if you not yet implement it.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'select',
                'default'     => 'no',
                'choices'     => [
                    'yes' => __('Yes', 'law-lib'),
                    'no'  => __('No', 'law-lib'),
                ],
            ],
            'facebook_comment_count' => [
                'label'       => __( 'Facebook Comment To Show', 'law-lib' ),
                'description' => __( 'How many facebook comment to show.', 'law-lib' ),
                'priority'    => 10,
                'transport'   => 'refresh',
                'type'        => 'number',
                'default'     => '10',
                'sanitize_callback' => function($e) {
                    if (!is_numeric($e)) {
                        return 10;
                    }
                    $e = absint($e);
                    $e = $e < 1 ? 1 : $e;
                    return $e > 50 ? 50 : $e;
                }
            ],
        ]
    ]
];
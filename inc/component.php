<?php

use ArrayIterator\WP\Gear\Components\Amp\Carousel;
use ArrayIterator\WP\Gear\Service;

if ( ! defined('ABSPATH')) :
    return;
endif;

if ( ! function_exists('law_lib_component_button_sidebar')) {
    function law_lib_component_button_sidebar(string $id, $echo = true, string $label = null): string
    {
        $id     = esc_attr($id);
        $label  = esc_attr($label ?? __('Toggle Sidebar', 'law-lib'));
        $button = <<<HTML
    <button class="sidebar-open-btn animate-toggle" on="tap:{$id}" aria-label="{$label}">
        <span></span>
        <span></span>
        <span></span>
    </button>
HTML;
        if ($echo) {
            echo $button;
        }

        return $button;
    }
}

/*
if (!function_exists('law_lib_component_slider')) {
    function law_lib_component_slider(
        array $post_ids,
        string $id = null,
        array $args = []
    ) : string {
        $post_id = array_filter($post_ids, 'is_numeric');
        if (empty($post_id)) {
            return '';
        }
        $carousel = Service::get(Carousel::class);
        $carouselList = $carousel->create(
            $id,
            $carousel::LAYOUT_RESPONSIVE,
            $carousel::TYPE_SLIDES,
            $args
        );
        return $carouselList->toHtml();
    }
}
*/

if ( ! function_exists('law_lib_component_get_all_category_as_key_name')) {
    function law_lib_component_get_all_category_as_key_name()
    {
        return Law_Lib_Meta_Data::categoryList();
    }
}

if ( ! function_exists('law_lib_component_get_all_tags_as_key_name')) {
    function law_lib_component_get_all_tags_as_key_name()
    {
        return Law_Lib_Meta_Data::tagList();
    }
}

if ( ! function_exists('law_lib_component_get_all_category_as_key_name_sanitize')) {
    function law_lib_component_get_all_category_as_key_name_sanitize($input)
    {
        $valid = law_lib_component_get_all_category_as_key_name();
        foreach ($input as $key => $value) {
            if ( ! array_key_exists($value, $valid)) {
                unset($input[$key]);
            }
        }
        return array_map('absint', array_unique($input));
    }
}

if ( ! function_exists('law_lib_component_get_all_tags_as_key_name_sanitize')) {
    function law_lib_component_get_all_tags_as_key_name_sanitize($input)
    {
        $valid = law_lib_component_get_all_tags_as_key_name();
        foreach ($input as $key => $value) {
            if ( ! array_key_exists($value, $valid)) {
                unset($input[$key]);
            }
        }
        return array_map('absint', array_unique($input));
    }
}

if (!function_exists('law_lib_component_get_default_image')) {
    function law_lib_component_get_default_image($thumbnails = false)
    {
        static $image_sizes = null;
        if ( ! is_array($image_sizes)) {
            $image_sizes = [];
            foreach (wp_get_additional_image_sizes() as $key => $item) {
                $path = "/assets/images/thumbnails/{$item['width']}x{$item['height']}.png";
                $file = get_theme_file_path($path);
                if ( ! $file || ! file_exists($file)) {
                    continue;
                }
                $uri = get_theme_file_uri($path);
                if ($uri) {
                    continue;
                }
                $image_sizes[$key] = $uri;
            }
        }
        if (is_string($thumbnails)) {
            return $image_sizes[$thumbnails] ?? false;
        }
        return $image_sizes;
    }
}

if (!function_exists('law_lib_component_shorthand_replace')) {
    function law_lib_component_shorthand_replace($content)
    {
        $replacer = apply_filters(
            'law_lib_shorthand_replace_content',
            [
                '{copy}' => '&copy;',
                '{year}' => date('Y'),
                '{url}'  => home_url(),
                '{name}' => get_bloginfo('name'),
            ]
        );

        return str_replace(array_keys($replacer), array_values($replacer), $content);
    }
}

if (!function_exists('law_lib_component_options_default')) {
    function law_lib_component_options_default()
    {
        static $options;
        if (is_array($options)) {
            return $options;
        }
        $options = include __DIR__ . '/customizer/options.php';
        $sections         = locate_template('inc/customizer/options.php', false, false);
        if ($sections) {
            $options = array_merge((array)include $sections, $options);
        }

        return $options;
    }
}

if (!function_exists('law_lib_component_option')) {
    function law_lib_component_option($option, $subKeyName = null, $default = null)
    {
        static $options;
        if ( ! is_array($options)) {
            foreach (law_lib_component_options_default() as $key => $item) {
                if ( ! is_array($item) || ! isset($item['settings']) || ! is_array($item['settings'])) {
                    continue;
                }
                $options[$key] = [];
                foreach ($item['settings'] as $name => $i) {
                    if ( ! is_array($i) || ! array_key_exists('default', $i)) {
                        continue;
                    }
                    $options[$key][$name] = $i['default'];
                }
            }
        }

        $opt = get_theme_mod($option);
        if (isset($options[$option])) {
            $opt = array_merge($options[$option], (array)$opt);
        } elseif (! is_array($opt)) {
            return $subKeyName ? ($options[$subKeyName]??$default) : ($opt ?? []);
        }

        return $subKeyName ? ($opt[$subKeyName]??$default) : ($opt ?? []);
    }
}

if (!function_exists('law_lib_component_is_blog_post')) {
    function law_lib_component_is_blog_post($post_id = null) : bool
    {
        if (!law_lib_logic_enable_blog_filtering()) {
            return false;
        }
        $post_id = Law_Lib_Meta_Data::determinePostId($post_id);
        if ( ! $post_id) {
            return false;
        }

        $_home_mods       = law_lib_component_option('homepage');
        $_blog_categories = $_home_mods['blog_categories'] ?? [];
        $_blog_categories = ! is_array($_blog_categories) ? [] : $_blog_categories;
        $_blog_categories = array_map('absint',
            law_lib_component_get_all_category_as_key_name_sanitize($_blog_categories));
        $cats             = get_the_terms($post_id, 'category');
        if ( ! $cats || is_wp_error($cats)) {
            return false;
        }
        foreach ($cats as $wp_term) {
            if (in_array($wp_term->term_id, $_blog_categories)) {
                return true;
            }
        }

        return false;
    }
}

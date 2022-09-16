<?php

use AmpProject\AmpWP\Option;

if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_logic_is_amp')) {
    function law_lib_logic_is_amp()
    {
        static $is_amp = null;
        if ($is_amp === null) {
            $is_amp = function_exists('amp_is_request') && amp_is_request();
        }

        return $is_amp;
    }
}
if (!function_exists('law_lib_logic_is_standard_amp')) {
    function law_lib_logic_is_standard_amp() : bool
    {
        $current_mode = null;
        $mode = 'standard';
        if (class_exists(AMP_Options_Manager::class)
            && class_exists(AMP_Theme_Support::class)
        ) {
            $mode_name = class_exists(Option::class) ? Option::THEME_SUPPORT : 'theme_support';
            $mode = AMP_Theme_Support::STANDARD_MODE_SLUG;
            $current_mode = AMP_Options_Manager::get_option($mode_name);

        }
        return $current_mode === $mode;
    }
}

if (!function_exists('law_lib_logic_is_amp_active')) {
    function law_lib_logic_is_amp_active()
    {
        static $installed = null;
        if ($installed === null) {
            if (law_lib_logic_is_amp_installed()) {
                $installed = is_plugin_active('amp/amp.php');
            }
        }

        return $installed;
    }
}

if (!function_exists('law_lib_logic_is_amp_installed')) {
    function law_lib_logic_is_amp_installed($force = false)
    {
        static $installed = null;
        if ($force || $installed === null) {
            if (!function_exists('get_plugins')) {
                include_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            $installed_plugins = get_plugins();

            $slug = 'amp/amp.php';
            $installed = array_key_exists($slug, $installed_plugins) || in_array($slug, $installed_plugins, true);
        }
        return $installed;
    }
}

if (!function_exists('law_lib_logic_is_in_network')) {
    function law_lib_logic_is_in_network() : bool
    {
        return function_exists('get_current_screen') && get_current_screen() && get_current_screen()->in_admin('network');
    }
}

/* ------------------------------------------------------------
 * HOME PAGE
 * ------------------------------------------------------------
 */

if (!function_exists('law_lib_logic_enable_blog_filtering')) {
    function law_lib_logic_enable_blog_filtering(): bool
    {
        return law_lib_component_option(
                   'homepage',
                   'blog_filtering',
                   'no'
               ) === 'yes';
    }
}

if (!function_exists('law_lib_logic_enable_blog_posts')) {
    function law_lib_logic_enable_blog_posts(): bool
    {
        return law_lib_component_option('homepage', 'show_blogs', 'yes') !== 'no';
    }
}

if (!function_exists('law_lib_logic_home_show_post_count')) {
    function law_lib_logic_home_show_post_count(): int
    {
        $_show_blog_post_count = law_lib_component_option('homepage', 'show_blog_post_count', 'yes');
        $_show_blog_post_count = ! is_numeric($_show_blog_post_count) ? 10 : absint($_show_blog_post_count);
        $_show_blog_post_count = $_show_blog_post_count < 3 ? 3 : $_show_blog_post_count;

        return $_show_blog_post_count > 50 ? 50 : $_show_blog_post_count;
    }
}
if (!function_exists('law_lib_logic_blog_categories')) {
    function law_lib_logic_blog_categories(): array
    {
        $_blog_categories = law_lib_component_option('homepage', 'blog_categories', []);
        $_blog_categories = !is_array($_blog_categories) ? [] : $_blog_categories;
        return array_map('absint', law_lib_component_get_all_category_as_key_name_sanitize($_blog_categories));
    }
}

if (!function_exists('law_lib_logic_blog_excerpt')) {
    function law_lib_logic_blog_excerpt(): bool
    {
        return law_lib_component_option('homepage', 'show_excerpt', 'yes') !== 'no';
    }
}

if (!function_exists('law_lib_logic_is_homepage')) {
    function law_lib_logic_is_homepage(): bool
    {
        return !is_paged() && (
                is_front_page() || is_home()
           );
    }
}
if (!function_exists('law_lib_logic_loop_setting')) {
    function law_lib_logic_loop_setting(string $setting, $default = null): bool
    {
        $_setup = law_lib_logic_is_homepage() && empty($wp_query->has_front_page_data)
            ? 'homepage'
            : (is_single() ? 'post' : 'archive');

        switch ($setting) {
            case 'show_excerpt':
            case 'show_thumbnail':
            case 'show_date':
            case 'show_author':
            case 'show_author_bio':
            case 'show_category':
            case 'show_archive_description':
                return law_lib_component_option($_setup, $setting, 'yes') !== 'no';
        }

        return law_lib_component_option($_setup, $setting, $default) === 'yes';
    }
}


if (!function_exists('law_lib_logic_nice_number')) {
    function law_lib_logic_nice_number($n, $default = false)
    {
        if (is_string($n)) {
            // first strip any formatting;
            $n = (0 + str_replace(",", "", $n));
        }

        // is this a number?
        if (!is_numeric($n)) {
            return $default;
        }

        // now filter it;
        $short = '';
        $name = '';
        if ($n > 1000000000000) {
            $val = round(($n / 1000000000000), 1);
            $short = 'T';
            $name = _n('Trillion', 'Trillions', $val, 'law-lib');
        } elseif ($n > 1000000000) {
            $val = round(($n / 1000000000), 1);
            $short = 'B';
            $name = _n('Billion', 'Billions', $val, 'law-lib');
        } elseif ($n > 1000000) {
            $val = round(($n / 1000000), 1);
            $short = 'T';
            $name = _n('Million', 'Millions', $val, 'law-lib');
        } elseif ($n > 1000) {
            $val = round(($n / 1000), 1);
            $short = 'K';
            $name = _n('Thousand', 'Thousands', $val, 'law-lib');
        } else {
            $val = $n;
        }

        return apply_filters(
            'law_lib_nice_number_result',
            sprintf(
                '<span class="view-formatted">%1$s</span><span class="view-short-name" data-short-name="%2$s">%3$s</span>',
                number_format_i18n($val),
                esc_attr($short),
                $short
            ),
            $val,
            $short,
            $name
        );
    }
}

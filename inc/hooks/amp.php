<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

use AmpProject\AmpWP\DevTools\UserAccess;
use AmpProject\AmpWP\Option;
use AmpProject\AmpWP\Services;

/**
 * Hooks for after theme activated
 */
if (!function_exists('law_lib_amp_switch_theme')) {
    function law_lib_amp_switch_theme()
    {
        if (class_exists(AMP_Options_Manager::class)
            && class_exists(AMP_Theme_Support::class)
        ) {
            $mode_name = class_exists(Option::class) ? Option::THEME_SUPPORT : 'theme_support';
            $mode = AMP_Theme_Support::STANDARD_MODE_SLUG;
            $current_mode = AMP_Options_Manager::get_option($mode_name);
            if ($current_mode !== $mode) {
                AMP_Options_Manager::update_option(
                    $mode_name,
                    $mode
                );
            }
        } else {
            $options_name = 'amp-options';
            $mode = 'standard';
            $mode_name = 'theme_support';
            $options = get_option($options_name);
            if (!is_array($options)) {
                $options = [];
            }
            $modes = $options[$mode_name]??null;
            if ($modes !== $mode) {
                $options[$mode_name] = $modes;
                update_option($options_name, $options);
            }
        }
        if (law_lib_logic_is_amp_installed()) {
            activate_plugin('amp/amp.php');
        }
    }
}

add_action('after_switch_theme', 'law_lib_amp_switch_theme');

/**
 * Remove view as no amp
 */
if (!function_exists('law_lib_amp_remove_admin_bar_menu_item')) {
    /**
     * @param WP_Admin_Bar $wp_admin_bar
     *
     * @return WP_Admin_Bar
     */
    function law_lib_amp_remove_admin_bar_menu_item($wp_admin_bar)
    {
        if ( is_admin()) {
            return $wp_admin_bar;
        }
        // $service = Services::get( 'dev_tools.user_access' );
        if (!class_exists(Services::class)
            || !function_exists('amp_is_available')
            || ! amp_is_available()
        ) {
            return $wp_admin_bar;
        }
        $service = Services::get('dev_tools.user_access');
        if (!$service instanceof UserAccess || !$service->is_user_enabled()) {
            return $wp_admin_bar;
        }

        $wp_admin_bar->remove_node('amp-view');
        return $wp_admin_bar;
    }
}

add_action( 'admin_bar_menu', 'law_lib_amp_remove_admin_bar_menu_item', 201 );

/**
 * Remove no amp mode
 */
if (!function_exists('law_lib_amp_remove_get_amp')) {
    function law_lib_amp_remove_get_amp()
    {
        if (is_admin()
            || ($_GET['noamp'] ?? '') !== 'available'
        ) {
            return;
        }
        if (apply_filters('law_lib_disable_amp_query', true)) {
            unset($_GET['noamp']);
        }
    }
}
add_action('wp', 'law_lib_amp_remove_get_amp');

if (!function_exists('law_lib_amp_rest_dispatch_posts')) {
    /**
     * @param WP_REST_Response|WP_HTTP_Response $result
     * @param WP_REST_Server $server
     * @param WP_REST_Request $request
     *
     * @return mixed|void
     */
    function law_lib_amp_rest_dispatch_posts($result, WP_REST_Server $server, WP_REST_Request $request)
    {
        $matched_route = $result->get_matched_route();
        if ($result->status !== 200
            || ! $matched_route
            || !$result instanceof Law_Lib_Rest_Response
        ) {
            return $result;
        }

        if ($result->single_response) {
            $result->data = ['item' => $result->data];
        } else {
            $result->data = ['items' => $result->data] + $result->getMetaData();
        }

        return $result;
    }
}

add_filter('rest_post_dispatch', 'law_lib_amp_rest_dispatch_posts', PHP_INT_MAX, 3);

if (!function_exists('law_lib_amp_register_post_routes')) {
    function law_lib_amp_register_post_routes($post_type)
    {
        $law_lib_post_rest = new Law_Lib_Post_Rest($post_type);
        add_action('rest_api_init', [$law_lib_post_rest, 'register_routes']);
    }
}

add_action( 'registered_post_type', 'law_lib_amp_register_post_routes');

if (!function_exists('law_lib_amp_remove_generator')) {
    function law_lib_amp_remove_generator($post_type)
    {
        if (law_lib_component_option('misc', 'rank_amp_generator') == 'yes') {
            remove_action('wp_head', 'amp_add_generator_metadata', 20);
            remove_action('wp_head', 'wp_generator');
        }
    }
}

add_action('template_redirect', 'law_lib_amp_remove_generator');
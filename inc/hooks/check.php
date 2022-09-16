<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_check_admin_notice_amp_plugin_not_installed')) {
    function law_lib_check_admin_notice_amp_plugin_not_installed()
    {
        $info = [
            sprintf(
                __('Please install %s and activate plugin to make theme %s running properly.', 'law-lib'),
                sprintf('<strong>%s</strong>', 'amp'),
                sprintf('<strong>%s</strong>', wp_get_theme()->get('Name'))
            ),
            sprintf(
                __('Click %s to install %s plugin.', 'law-lib'),
                sprintf(
                    '<a href="%s">%s</a>',
                    wp_nonce_url(
                        self_admin_url('update.php?action=install-plugin&amp;plugin=amp'),
                        'install-plugin_amp'
                    ),
                    sprintf('<strong>%s</strong>', __('here', 'law-lib'))
                ),
                sprintf('<strong>%s</strong>', 'amp')
            ),
        ];
        law_lib_notice_info('<p>'.implode('</p><p>', $info).'</p>');
    }
}

if (!function_exists('law_lib_check_admin_notice_amp_plugin_not_active')) {
    function law_lib_check_admin_notice_amp_plugin_not_active()
    {
        $info = [
            sprintf(
                __('Please activate %s plugin to make theme %s running properly.', 'law-lib'),
                sprintf('<strong>%s</strong>', 'amp'),
                sprintf('<strong>%s</strong>', wp_get_theme()->get('Name'))
            ),
            sprintf(
                __('Click %s to activate %s plugin.', 'law-lib'),
                sprintf(
                    '<a href="%s">%s</a>',
                    wp_nonce_url(
                        'plugins.php?action=activate&amp;plugin='.urlencode('amp/amp.php'),
                        'activate-plugin_amp/amp.php'
                    ),
                    sprintf('<strong>%s</strong>', __('here', 'law-lib'))
                ),
                sprintf('<strong>%s</strong>', 'amp')
            ),
        ];
        law_lib_notice_info('<p>'.implode('</p><p>', $info).'</p>');
    }
}

if (!function_exists('law_lib_check_admin_notice_amp_plugin_not_standard_mode')) {
    function law_lib_check_admin_notice_amp_plugin_not_standard_mode()
    {
        $info = [
            sprintf(
                __('Please set as standard mode on %s plugin to make theme %s running properly.', 'law-lib'),
                sprintf('<strong>%s</strong>', 'amp'),
                sprintf('<strong>%s</strong>', wp_get_theme()->get('Name'))
            ),
        ];
        $menu_url = menu_page_url('amp-options', false);
        if ($menu_url) {
            $info[] = sprintf(
                __('Click %s to change %s plugin setting.', 'law-lib'),
                sprintf(
                    '<a href="%s">%s</a>',
                    sprintf('%s#template-mode-standard', $menu_url),
                    sprintf('<strong>%s</strong>', __('here', 'law-lib'))
                ),
                sprintf('<strong>%s</strong>', 'amp')
            );
        }
        law_lib_notice_info('<p>'.implode('</p><p>', $info).'</p>');
    }
}

if (!function_exists('law_lib_check_amp_plugin')) {
    function law_lib_check_amp_plugin()
    {
        if (!is_admin()) {
            return;
        }
        static $tell = false;
        if ($tell) {
            return;
        }
        $tell = true;
        // remove_action('admin_init', __FUNCTION__, 12);
        if ( ! law_lib_logic_is_amp_installed()) {
            if (current_user_can('activate_plugins')) {
                add_action('admin_notices', 'law_lib_check_admin_notice_amp_plugin_not_installed');
            }
        } elseif ( ! law_lib_logic_is_amp_active()) {
            $is_network = law_lib_logic_is_in_network();
            if ($is_network && current_user_can('manage_network_plugins')
                || !$is_network && current_user_can('activate_plugins')
            ) {
                add_action('admin_notices', 'law_lib_check_admin_notice_amp_plugin_not_active');
            }
        } elseif (!law_lib_logic_is_standard_amp()) {
            add_action('admin_notices', 'law_lib_check_admin_notice_amp_plugin_not_standard_mode');
        }
    }
}

add_action('admin_init', 'law_lib_check_amp_plugin', 12);

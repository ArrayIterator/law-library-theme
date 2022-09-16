<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_addition_remove_credit')) {
    function law_lib_addition_remove_credit()
    {
        if (law_lib_component_option('misc', 'remove_credit', 'yes') === 'no') {
            return;
        }

        add_filter('rank_math/sitemap/remove_credit', '__return_true');
        add_filter('rank_math/frontend/remove_credit_notice', '__return_true');
    }
}

add_action('init', 'law_lib_addition_remove_credit');

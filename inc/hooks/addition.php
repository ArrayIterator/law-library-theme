<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_addition_remove_credit')) {
    function law_lib_addition_remove_credit()
    {
        if (law_lib_component_option('misc', 'rank_math_sitemap_credit', 'yes') !== 'no') {
            add_filter('rank_math/sitemap/remove_credit', '__return_true');
            add_filter('rank_math/frontend/remove_credit_notice', '__return_true');
        }
        if (law_lib_component_option('misc', 'disable_w3tc_comment_html_credit', 'yes') !== 'no') {
            add_filter('w3tc_footer_comment', 'law_lib_return_array');
        }

    }
}

add_action('init', 'law_lib_addition_remove_credit');

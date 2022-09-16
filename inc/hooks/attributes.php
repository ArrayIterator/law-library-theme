<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_attributes_body_class')) {
    function law_lib_attributes_body_class($body_class)
    {
        return $body_class;
    }
}

add_filter('body_class', 'law_lib_attributes_body_class');
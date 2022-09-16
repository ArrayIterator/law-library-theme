<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_return_right')) {
    function law_lib_return_right()
    {
        return 'right';
    }
}
if (!function_exists('law_lib_return_left')) {
    function law_lib_return_left()
    {
        return 'left';
    }
}
if (!function_exists('law_lib_return_top')) {
    function law_lib_return_top()
    {
        return 'top';
    }
}
if (!function_exists('law_lib_return_bottom')) {
    function law_lib_return_bottom()
    {
        return 'bottom';
    }
}
if (!function_exists('law_lib_return_array')) {
    function law_lib_return_array()
    {
        return [];
    }
}
if (!function_exists('law_lib_return_object')) {
    function law_lib_return_object()
    {
        return new stdClass();
    }
}

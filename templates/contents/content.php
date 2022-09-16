<?php
if (!defined('ABSPATH')) :
    return;
endif;

/* DOING LOOP */
$_post_id = $args['post_id']??get_the_ID();
$args['post_id'] = $_post_id;
$_template = is_404() ? 'none' : (is_singular() ? 'single' : 'loop');
get_template_part('/templates/contents/content', $_template, $args);

<?php
if ( ! defined('ABSPATH')) :
    return;
endif;
$_post_id = false;
?>
<div class="not-found-404" id="page-notfound">
    <?php get_template_part('templates/partials/entries/entry-header', get_post_format(), ['post_id' => $_post_id]);?>
    <?php get_template_part('templates/partials/entries/entry-content', get_post_format(), ['post_id' => $_post_id]);?>
    <?php get_template_part('templates/partials/entries/entry-footer', get_post_format(), ['post_id' => $_post_id]);?>
</div>

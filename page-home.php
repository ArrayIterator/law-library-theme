<?php
/**
 * Template Name: Home Page
 */
if (!defined('ABSPATH')) :
    return;
endif;

// if is paged
if (is_paged() || !is_front_page()) :
    if (get_template_part('page') === false) {
        get_template_part('index');
    }
    return;
endif;

$_post_id = get_the_ID();
$_permalink = get_permalink($_post_id);
$_enable_filtering = law_lib_logic_enable_blog_filtering();

?>
<?php get_header(); ?>

<div class="container singular-container page-container homepage-container">
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('templates/contents/content', is_front_page() ? 'home' : get_post_format(), ['post_id' => $_post_id]);?>
    <?php endwhile;?>
</div>
<?php if (!$_enable_filtering || law_lib_logic_enable_blog_posts()) : ?>
<?php
global $wp_query;

$_blog_categories = law_lib_logic_blog_categories();
$_sidebar_name = 'homepage-sidebar';
$_is_has_sidebar = is_active_sidebar($_sidebar_name);

$wp_query->query['ignore_sticky_posts'] = true;
if ($_enable_filtering && ! empty($_blog_categories)) {
    $wp_query->query['category__in'] = $_blog_categories;
    $wp_query->query['per_page'] = law_lib_logic_home_show_post_count();
}

$wp_query->query['p'] = 0;
$wp_query->query['page_id'] = 0;
$_queried_object = $wp_query->queried_object;
$_queried_object_id = $wp_query->queried_object_id;
$is_preview = $wp_query->is_preview() ? $wp_query->query['preview']??true : false;
unset($wp_query->query['preview']);
$wp_query->query($wp_query->query);
if ($is_preview) {
    $wp_query->is_preview = true;
}
$wp_query->is_category = false;
$wp_query->is_archive = false;
$wp_query->is_home = true;
$wp_query->old_queried_object = $_queried_object;
$wp_query->old_queried_object_id = $_queried_object_id;
// add frontpage data
$wp_query->has_front_page_data = true;
$wp_query->is_page = false;
if (!$_enable_filtering) {
    get_template_part('index');
    return;
}
?>
<div class="content-blog-posts content-wrap container loop-container homepage-container <?= $_is_has_sidebar ? 'active-sidebar' : 'no-sidebar';?>">
    <?php do_action('before_container');?>
    <div class="content-container">
        <?php do_action('before_loop');?>
        <?php while (have_posts()) : the_post();?>
            <?php get_template_part('templates/contents/content', get_post_format(), ['post_id' => get_the_ID()]);?>
        <?php endwhile;?>
        <?php do_action('after_loop');?>
    </div>
    <!-- .content-container -->
    <?php do_action('after_container');?>
    <?php if ($_is_has_sidebar) : ?>
        <?php do_action('before_sidebar_section', $_sidebar_name);?>
        <div class="sidebar-section">
            <?php do_action('before_sidebar_entry', $_sidebar_name);?>
            <div class="sidebar-entry">
                <?php do_action('before_sidebar_content', $_sidebar_name);?>
                <?php dynamic_sidebar($_sidebar_name);?>
                <?php do_action('after_sidebar_content', $_sidebar_name);?>
            </div>
            <!-- .sidebar-entry -->
            <?php do_action('after_sidebar_entry', $_sidebar_name);?>
        </div>
        <!-- .sidebar-section -->
        <?php do_action('after_sidebar_section', $_sidebar_name);?>
    <?php endif;?>
<?php endif;?>
</div>
<?php

get_footer();

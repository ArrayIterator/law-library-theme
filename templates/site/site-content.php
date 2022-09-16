<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_sidebar_name = 'archive-sidebar';
$_current_mode   = 'archive';
$_page_mode      = 'loop';
$_additional_class = '';
$_object = [
    'is_author' => null,
    'is_category' => null,
    'is_day' => null,
    'is_month' => null,
    'is_year' => null,
    'is_tag' => null,
    'is_search' => 'search',
    'is_404' => 'notfound',
    'is_singular' => 'singular',
];
foreach ($_object as $_object_key => $_p_mode) {
    if (!call_user_func($_object_key)) {
        continue;
    }
    if ($_p_mode) {
        $_page_mode = $_p_mode === 'search' ? $_page_mode : $_p_mode;
        $_current_mode = $_p_mode;
        $_sidebar_name = "{$_p_mode}-sidebar";
    }
    break;
}
if (is_page()) {
    $_page_mode = 'page';
    $_sidebar_name = 'page-sidebar';
} elseif (is_single()) {
    $_page_mode     = 'single';
    $_is_blog_post = law_lib_component_is_blog_post(get_the_ID());
    $_sidebar_name = $_is_blog_post ? 'blog-sidebar' : 'post-sidebar';
    $_additional_class = $_is_blog_post ? ' content-blog-type' :'';
}

$_is_has_sidebar = is_active_sidebar($_sidebar_name);
?>
<div class="container content-wrap <?= $_current_mode;?>-container <?= $_page_mode;?>-container <?= $_is_has_sidebar ? 'active-sidebar' : 'no-sidebar';?><?= $_additional_class;?>">
    <?php do_action('before_container');?>
    <div class="content-container">
        <?php do_action('before_loop');?>
        <?php if (is_singular()) :?>
            <?php get_template_part('templates/contents/content', get_post_format(), ['post_id' => get_the_ID()]);?>
        <?php elseif (!have_posts()) : ?>
            <?php get_template_part('templates/contents/content', 'none');?>
        <?php else : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('templates/contents/content', get_post_format(), ['post_id' => get_the_ID()]);?>
            <?php endwhile;?>
        <?php endif;?>
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
</div>

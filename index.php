<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
<?php get_header(); ?>
<?php
$_is_search = is_search();
$_is_singular = is_singular();
$_is_archive = is_archive();
if ($_is_singular || $_is_search || $_is_archive) {
    get_template_part('templates/site/site-content');
} else {
$_is_404 = is_404();
$_sidebar_name = $_is_404
    ? 'notfound-sidebar'
    : 'archive-sidebar';
$_is_has_sidebar = is_active_sidebar($_sidebar_name);
?>
<div class="container content-wrap content-index loop-container <?= $_is_has_sidebar ? 'active-sidebar' : 'no-sidebar';?>">
    <?php do_action('before_container');?>
    <div class="content-container">
        <?php do_action('before_loop');?>
        <?php
        if (!$_is_404 && have_posts()) :?>
            <?php
            while (have_posts()) : the_post();
            ?>
                <?php get_template_part(
                    'templates/contents/content',
                    get_post_format(),
                    ['post_id' => get_the_ID()]);
                ?>
            <?php endwhile;?>
        <?php else : ?>
            <?php get_template_part('templates/contents/content', 'none');?>
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
<?php
}
get_footer();

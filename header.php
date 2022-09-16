<?php
if (!defined('ABSPATH')) :
    return;
endif;
$_has_mobile_menu = has_nav_menu('mobile-menu');
$_has_mobile_menu = $_has_mobile_menu ? ' has-mobile-menu' : '';
$_has_mobile_menu = "site wrap $_has_mobile_menu";
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="<?= $_has_mobile_menu;?>" [class]="searchMobileShown ? '<?= $_has_mobile_menu;?> search-opened' : '<?= $_has_mobile_menu;?>'">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'law-lib' ); ?></a>
    <?php get_template_part('templates/site/site-header', get_post_format());?>

    <?php do_action('before_content');?>

    <div id="content" class="site-content">
        <?php do_action('before_primary');?>

        <div id="primary" class="content-area">
            <?php do_action('before_main');?>

            <main id="main" class="site-main" role="main">

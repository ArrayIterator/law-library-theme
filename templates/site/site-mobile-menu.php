<?php
if (!defined('ABSPATH')) :
    return;
endif;
if (!has_nav_menu('mobile-menu')) {
    return;
}
?>
<div id="mobile-menu" class="mobile-menu">
    <?php do_action('law_lib_before_menu_mobile');?>
    <?php
    wp_nav_menu([
        'theme_location' => 'mobile-menu',
        'menu' => 'mobile-menu',
        'menu_id' => 'mobile-menu-navigation',
        'menu_class' => 'mobile-navigation-menu',
        'container_class' => 'mobile-menu-container',
        'depth' => 1,
        'walker' => Law_Lib_Mobile_Menu_Walker::class,
    ]);?>
    <?php do_action('law_lib_after_menu_mobile');?>
</div>
<!-- #mobile-menu -->

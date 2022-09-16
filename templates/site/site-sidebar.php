<?php
if (!defined('ABSPATH')) :
    return;
endif;
$side = apply_filters('law_lib_template_sidebar_side', 'left');
?>
<amp-sidebar id="sidebar-menu" class="sidebar-menu" layout="nodisplay" side="<?= esc_attr($side);?>">
    <?php do_action('law_lib_before_menu_sidebar', $side);?>
    <?php wp_nav_menu([
        'menu_id' => 'sidebar-menu-navigation',
        'theme_location' => 'sidebar-menu',
        'menu' => 'sidebar-menu',
        'walker' => Law_Lib_Sidebar_Amp_Walker::class,
        'side' => $side,
    ]);?>
    <?php do_action('law_lib_after_menu_sidebar', $side);?>
</amp-sidebar>
<!-- #sidebar-menu -->

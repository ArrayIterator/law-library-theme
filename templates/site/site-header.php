<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
    <?php do_action('before_site_header');?>
    <div id="header" class="main-header">
        <div id="top-bar" class="top-bar hide-mobile navigation">
            <div class="container">
                <div class="search-bar-wrapper">
                    <div id="top-menu" class="top-menu" [hidden]="searchBarShown">
                        <?php
                        wp_nav_menu([
                            'menu_id' => 'top-menu-navigation',
                            'theme_location' => 'top-menu',
                            'menu' => 'top-menu',
                            'container' => 'nav',
                            'menu_class' => 'menu',
                            'container_class' => 'menu-container',
                            'depth' => 1,
                            'walker' => new Law_Lib_Mega_Menu_Walker(),
                        ]);
                        ?>
                    </div>
                    <div class="search-bar hide-mobile" hidden id="main-navigation-form-search" [hidden]="!searchBarShown">
                        <?php get_search_form(
                            [
                                'placeholder' => _x('Type To Search & Enter ... ', 'label', 'law-lib'),
                                'no-filter' => true
                            ]
                        );?>
                    </div>
                    <div class="search-button-wrap">
                        <button role="button" aria-label="toggle" on="tap:AMP.setState({searchBarShown: !searchBarShown})">
                            <i class="ic-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="main-logo" class="main-logo hide-mobile">
            <div class="main-logo-container container">
                <?php
                $_logo = get_custom_logo();
                if ( $_logo && is_string( $_logo ) ) :
                    ?>
                    <?= $_logo;?>
                <?php else :
                    echo apply_filters(
                        'law_lib_logo_home_link',
                        sprintf(
                            '<a href="%1$s" rel="home" aria-label="home" class="main-logo-home-link">%2$s</a>',
                            esc_attr(home_url()),
                            apply_filters(
                                'law_lib_sidebar_home_link_text',
                                get_bloginfo('name')
                            )
                        )
                    );
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php do_action('before_main_navigation');?>
    <div id="main-navigation" class="navigation navigation-top">
        <div class="main-navigation-menu" id="main-navigation-menu">
            <amp-mega-menu height="60" layout="fixed-height">
            <?php
            echo wp_nav_menu([
                'menu_id' => 'main-menu-navigation',
                'theme_location' => 'main-menu',
                'menu' => 'main-menu',
                'container' => 'nav',
                'container_class' => 'menu-container',
                'menu_class' => 'menu container',
                //'menu_class' => 'menu',
                'walker' => new Law_Lib_Mega_Menu_Walker(),
                'depth' => 3
            ]);
            ?>
            </amp-mega-menu>
        </div>
        <div id="mobile-logo" class="main-logo hide-desktop">
            <?php if (law_lib_component_option('misc', 'disable_mobile_logo') !== 'yes') :?>
            <div class="main-logo-container">
                <?php
                $_mobile_logo      = get_theme_mod( 'custom-mobile-logo' );
                $_custom_logo_args = get_theme_support( 'custom-mobile-logo' );
                $_custom_logo_args = $_custom_logo_args ? ( $_custom_logo_args[0] ?? [] ) : [];
                $_args = [
                    'width' => $_custom_logo_args['width'],
                    'height' => $_custom_logo_args['height'],
                    'alt' => get_bloginfo( 'name' ),
                ];
                $_image = is_numeric($_mobile_logo)
                    ? wp_get_attachment_image( $_mobile_logo, 'full', false, $_args )
                    : '';
                if ( $_image && is_string( $_image ) ) :
                    ?>
                    <a href="<?= esc_url( home_url() ); ?>"
                       aria-label="<?php esc_attr_e( 'Homepage', 'law-lib' ); ?>"
                       class="mobile-home-link" rel="home">
                        <?= $_image;?>
                    </a>
                <?php else :
                echo apply_filters(
                    'law_lib_logo_home_link',
                    sprintf(
                        '<a href="%1$s" rel="home" aria-label="home" class="main-logo-home-link">%2$s</a>',
                        esc_attr(home_url()),
                        apply_filters(
                            'law_lib_sidebar_home_link_text',
                            get_bloginfo('name')
                        )
                    )
                )
                ?>
                <?php endif;?>
            </div>
            <?php endif; ?>
        </div>
        <!-- #mobile-logo -->
        <?php law_lib_component_button_sidebar('sidebar-menu', true, __('Toggle Sidebar', 'law-lib')); ?>
    </div>
    <!-- #main-navigation -->
    <?php do_action('after_main_navigation');?>
    <!-- #header -->
    <?php do_action('after_site_header');?>
<?php

<?php
if (!defined('ABSPATH')) :
    return;
endif;

if ( ! function_exists( 'menu_law_menu_icon_lib_item_icon_field' ) ) {
    function menu_law_menu_icon_lib_item_icon_field( $item_id, $item ) {
        $menu_item_desc = get_post_meta( $item_id, 'law_lib_menu_item_icon', true );
        ?>
        <div style="clear: both;" class="menu_law_lib_icon_div" data-id="<?php echo $item_id; ?>">
            <label for="menu_law_lib_item_icon_<?php echo $item_id; ?>" class="description"><?php _e( "Menu Icon",
                    'law-lib' ); ?></label><br/>
            <div class="logged-input-holder">
                <input type="hidden" readonly class="nav-menu-id" value="<?php echo $item_id; ?>">
                <button type="button" class="icon-chooser"
                        id="menu_law_lib_item_icon_<?php echo $item_id; ?>"><?php _e( 'SELECT ICON',
                        'law-lib' ); ?></button>
                <?php
                $icon = is_array( $menu_item_desc ) && isset( $menu_item_desc['icon'] )
                        && is_string( $menu_item_desc['icon'] )
                        && trim( $menu_item_desc['icon'] ) !== ''
                    ? $menu_item_desc['icon'] : '';
                ?>
                <div class="law_lib_icon_preview">
                    <label for="law-lib-icon-list-<?= $item_id; ?>" class="screen-reader-text"></label>
                    <input class="law_lib_icon_input"
                           placeholder="<?php esc_attr_e( 'please select an icon', 'law-lib' ); ?>"
                           id="law-lib-icon-list-<?= $item_id; ?>" readonly type="text"
                           name="law_lib_menu_item_icon[<?php echo $item_id; ?>][icon]"
                           value="<?php echo esc_attr( $icon ); ?>"/>
                    <div class="law_lib_icon_preview_container">
                        <?php
                        if ( $icon ) :
                            $color = isset( $menu_item_desc['color'] )
                                     && is_string( $menu_item_desc['color'] )
                                     && preg_match( '~^#([0-9a-f]{3}|[0-9a-f]{6})~i', trim( $menu_item_desc['color'] ) )
                                ? $menu_item_desc['color']
                                : '';
                            ?>
                            <div class="law_lib_icon"
                                 data-icon="<?= esc_attr( $icon ); ?>" <?= $color ? 'style="color:' . esc_attr( $color ) . ';"' : ''; ?>>
                                <i class="<?= esc_attr( $icon ); ?>"></i>
                            </div>
                            <div class="law_lib_close">&times;</div>
                            <label>
                                <input type="text" maxlength="7" class="law-lib-color-picker"
                                       name="law_lib_menu_item_icon[<?php echo $item_id; ?>][color]"
                                       value="<?php echo esc_attr( $menu_item_desc['color'] ?? '' ); ?>">
                            </label>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
add_action( 'wp_nav_menu_item_custom_fields', 'menu_law_menu_icon_lib_item_icon_field', 10, 2 );

if ( ! function_exists( 'menu_law_lib_save_item_icon' ) ) {
    function menu_law_lib_save_item_icon( $menu_id, $menu_item_db_id ) {
        $data = $_POST['law_lib_menu_item_icon'][ $menu_item_db_id ] ?? null;
        if ( is_array( $data ) ) {
            $sanitized_data = array_map( 'sanitize_text_field', $data );
            update_post_meta( $menu_item_db_id, 'law_lib_menu_item_icon', $sanitized_data );
        } else {
            delete_post_meta( $menu_item_db_id, 'law_lib_menu_item_icon' );
        }
    }
}
add_action( 'wp_update_nav_menu_item', 'menu_law_lib_save_item_icon', 10, 2 );

if ( ! function_exists( 'menu_law_lib_show_item' ) ) {
    function menu_law_lib_show_item( $title, $item ) {
        if ( is_object( $item ) && isset( $item->ID ) ) {
            $menu_item_desc = get_post_meta( $item->ID, 'law_lib_menu_item_icon', true );
            if ( ! empty( $menu_item_desc['icon'] ) && is_string( $menu_item_desc['icon'] ) ) {
                $icons = law_lib_menu_icon_get_icons();
                if ( ! isset( $icons[ $menu_item_desc['icon'] ] ) ) {
                    return $title;
                }
                $color = isset( $menu_item_desc['color'] )
                         && is_string( $menu_item_desc['color'] )
                         && preg_match( '~^\s*#([0-9a-f]{3}|[0-9a-f]{6})\s*~i', $menu_item_desc['color'] )
                    ? 'style="color:' . $menu_item_desc['color'] . '"'
                    : '';
                $title = '<span class="menu-item-icon" ' . $color . '>'
                         . '<i class="' . esc_attr( $menu_item_desc['icon'] ) . '"></i>'
                         . '</span>' . $title;
            }
        }

        return $title;
    }
}
add_filter( 'nav_menu_item_title', 'menu_law_lib_show_item', 10, 2 );

if (!function_exists('law_lib_menu_icon_clear_cache')) {
    function law_lib_menu_icon_clear_cache()
    {
        wp_cache_delete('law-lib-fontello-list', 'law-lib');
    }
}

add_action('_core_updated_successfully', 'law_lib_menu_icon_clear_cache');

if ( ! function_exists('law_lib_menu_icon_get_icons') ) {
    function law_lib_menu_icon_get_icons() {
        static $icons = null;
        if ( is_array( $icons ) ) {
            return $icons;
        }

        $found = false;
        $cacheName = 'law-lib-fontello-list';
        $icons = wp_cache_get($cacheName, 'law-lib', false, $found );
        if ( ! is_array( $icons ) ) {
            if ( $found ) {
                wp_cache_delete($cacheName, 'law-lib' );
            }
            $icons = [];
            $wp_styles = wp_styles();
            // $default = ABSPATH .'/wp-includes/css/dashicons.min.css';
            $default = get_theme_file_path('/assets/fontello/fontello.css');
            $dash = $wp_styles->registered['law-lib-fontello']??null;
            // $dash = $dash ? ($dash->src??null) : null;
            $dash = $dash ? ($dash->src??null) : null;
            $contents = null;
            if (strpos($dash, site_url()) === 0) {
                $file = substr($dash, strlen(site_url()));
                $file = untrailingslashit(ABSPATH) . '/' . ltrim($file, '/');
                if ($file) {
                    $contents = file_get_contents($file);
                }
            }
            if (!$contents) {
                $contents = file_get_contents($default);
            }

            preg_match_all('~[.](ic[-][a-z0-9_\-]+):before\s*\{\s*content\s*:\s*[\"\'][\\\]([^\"\']+)[\"\']~', $contents, $match);
            unset($contents);
            $icons = [];
            if (!empty($match[1])) {
                foreach ($match[1] as $key => $item) {
                    $icons[$item] = $match[2][$key];
                }
            }

            wp_cache_set($cacheName, $icons, 'law-lib', 3600 * 24 );
            if (empty($icon)) {
                return $icons;
            }
        }

        return $icons;
    }
}

if ( ! function_exists( 'menu_law_lib_item_icon_json_render' ) ) {
    function menu_law_lib_item_icon_json_render() {
        if ( $GLOBALS['pagenow'] ?? '' == 'nav-menus.php' ) {
            $icons = law_lib_menu_icon_get_icons();
            if ( empty( $icons ) ) {
                return;
            }
            echo "<script>\n"
                 . 'window.law_lib_icon_font = '
                 . json_encode( array_keys( $icons ), JSON_UNESCAPED_SLASHES )
                 . ';'
                 . "</script>\n";
        }
    }
}
add_action( 'admin_print_scripts', 'menu_law_lib_item_icon_json_render' );

if ( ! function_exists( 'menu_law_lib_page_menu_args' ) ) {
    function menu_law_lib_page_menu_args( $args ) {
        if ( is_array( $args ) && isset( $args['menu'] ) ) { // && $args['menu'] === 'mobile-navigation') {
            $args['link_after'] = '<span class="sm-o"></span>';
        }

        return $args;
    }
}
add_filter( "wp_page_menu_args", 'menu_law_lib_page_menu_args' );

if ( ! function_exists('law_lib_menu_icon_nav_menu_item_args') ) {
    function law_lib_menu_icon_nav_menu_item_args( $args, $item ) {
        if ( is_object( $args ) && isset( $args->menu )
             // && $args->menu === 'mobile-navigation'
             && ! empty( $item->menu_item_parent )
        ) {
            $args->link_after = '<span class="sm-o"></span>';
        }

        return $args;
    }
}
add_filter( 'nav_menu_item_args', 'law_lib_menu_icon_nav_menu_item_args', 10, 2 );

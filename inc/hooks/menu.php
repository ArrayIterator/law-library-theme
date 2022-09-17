<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_menu_sidebar_args')) {
    function law_lib_menu_sidebar_args($args)
    {
        $walker = ($args['walker'] ?? '');
        if (empty($args['no_override']) && (
                $walker instanceof Law_Lib_Sidebar_Amp_Walker
                || is_string($walker) && strtolower($walker) === strtolower(Law_Lib_Sidebar_Amp_Walker::class)
                || is_subclass_of($walker, Law_Lib_Sidebar_Amp_Walker::class, true)
            )
        ) {
            $classes = ['navigation-menu'];
            $class_names = implode(' ', apply_filters('nav_menu_amp_nested_menu_class', $classes, $args, 0));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
            $args['container'] = '';
            $wrap_before = apply_filters('law_lib_menu_sidebar_first_list', '', $args);
            $wrap_before = is_string($wrap_before) ? trim($wrap_before) : '';
            $wrap_after = apply_filters('law_lib_menu_sidebar_last_list', '', $args);
            $wrap_after = is_string($wrap_after) ? trim($wrap_after) : '';
            if ($wrap_before) {
                $wrap_before = force_balance_tags($wrap_before);
                if (!preg_match('~<li[^>]*>.*</li>\s*$~sm', $wrap_before)
                    || preg_match('~%([0-9]+\$)?[bcdefghoxu]~i', $wrap_before)
                ) {
                    $wrap_before = '';
                }
            }
            if ($wrap_after) {
                $wrap_after = force_balance_tags($wrap_after);
                if (!preg_match('~<li[^>]*>.*</li>\s*$~sm', $wrap_after)
                    || preg_match('~%([0-9]+\$)?[bcdefghoxu]~i', $wrap_after)
                ) {
                    $wrap_after = '';
                }
            }
            $args['container'] = 'ul';
            $args['items_wrap'] = <<<HTML

<amp-nested-menu layout='fill'{$class_names}>
    <ul id='%1\$s' class='%2\$s'>
    {$wrap_before}
    %3\$s
    {$wrap_after}
    </ul>
</amp-nested-menu>

HTML;
            if (isset($args['menu_id']) && !has_nav_menu($args['menu'])) {
                $menu_class = sanitize_html_class($args['menu_class']??'');
                $menu_id    = sanitize_html_class($args['menu_id']);
                $args['before'] = <<<HTML
<amp-nested-menu layout="fill" class="navigation-menu i-amphtml-layout-fill i-amphtml-layout-size-defined" i-amphtml-layout="fill">
    <ul id="$menu_id" class="{$menu_class}">
    <li class="sub-menu-close home-page-list"><a href="https://cekhukum.com" rel="home" aria-label="home" class="sidebar-home-link">Cekhukum.com</a><span><i class="ic-close" on="tap:sidebar-menu.close" aria-label="Close" role="button" tabindex="0"></i></span></li><li class="menu-item search-box-menu"><span class="sidebar-search"><form role="search" aria-label="" method="get" class="search-form" action="https://cekhukum.com/" target="_top">
    <label class="screen-reader-text" for="navigation-search-form-2">Cari</label>
    <input type="search" id="navigation-search-form-2" class="form-control form-control-sm search-field" placeholder="Type to search ... " value="" name="s" maxlength="60">
    <button class="btn btn-sm ic-search" aria-label="submit"></button>
    </form>
</span></li>
HTML;
                $args['after'] = <<<HTML
</ul>
</amp-nested-menu>
HTML;

            }

            if (is_string($walker)) {
                $args['walker'] = new $walker();
            }
        } elseif ($walker && is_string($walker) && is_subclass_of($walker, Walker_Nav_Menu::class)) {
            $args['walker'] = new $walker();
        }

        return $args;
    }
}

add_filter( 'wp_nav_menu_args', 'law_lib_menu_sidebar_args' );

if (!class_exists('law_lib_menu_element_nav_menu_close_text')) {
    function law_lib_menu_element_nav_menu_close_text() : string
    {
        return sprintf(
            '<i class="ic-caret-left" aria-label="%s"></i>',
            esc_attr__('Close', 'law-lib')
        );
    }
}

add_filter('nav_menu_close_text', 'law_lib_menu_element_nav_menu_close_text');

if (!class_exists('law_lib_menu_element_nav_menu_open_text')) {
    function law_lib_menu_element_nav_menu_open_text() : string
    {
        return sprintf(
            '<i class="ic-caret-right" aria-label="%s"></i>',
            esc_attr__('Open', 'law-lib')
        );
    }
}

add_filter('nav_menu_open_text', 'law_lib_menu_element_nav_menu_open_text');

if (!function_exists('law_lib_menu_element_sidebar_first_list')) {
    function law_lib_menu_element_sidebar_first_list($item, $args)
    {
        if (law_lib_component_option('misc', 'disable_sidebar_text') === 'yes') {
            return $item;
        }

        if ($item || ! isset($args['menu'])) {
            return $item;
        }
        $side = $args['side'] ?? 'right';
        return sprintf(
            '<li class="sub-menu-close home-page-list">%s</li>',
            sprintf(
                $side == 'right'
                    ? '<span><i class="ic-close" on="tap:%1$s.close" aria-label="%2$s"></i></span>%3$s'
                    : '%3$s<span><i class="ic-close" on="tap:%1$s.close" aria-label="%2$s"></i></span>',
                esc_attr($args['menu']),
                esc_attr__('Close', 'law-lib'),
                apply_filters(
                    'law_lib_sidebar_home_link',
                    sprintf(
                        '<a href="%1$s" rel="home" aria-label="home" class="sidebar-home-link">%2$s</a>',
                        esc_attr(home_url()),
                        apply_filters(
                            'law_lib_sidebar_home_link_text',
                            get_bloginfo('name')
                        )
                    )
                )
            )
        );
    }
}

add_filter('law_lib_menu_sidebar_first_list', 'law_lib_menu_element_sidebar_first_list', 5, 2);

if (!function_exists('law_lib_menu_element_sidebar_last_list')) {
    function law_lib_menu_element_sidebar_last_list($item, $args): string
    {
        $item .= "<li class='menu-item search-box-menu'>";
        $item .= '<span class="sidebar-search">';
        $item .= get_search_form(['echo' => false]);
        $item .= '</div>';
        $item .= "</li>";
        return $item;
    }
}

add_filter('law_lib_menu_sidebar_first_list', 'law_lib_menu_element_sidebar_last_list', 6, 2);

if (!function_exists('law_lib_menu_element_sidebar_after_menu')) {
    function law_lib_menu_element_sidebar_after_menu()
    {
?>
        <div class="sidebar-search">
            <?php get_search_form();?>
        </div>
<?php
    }
}

//add_action('law_lib_after_menu_sidebar', 'law_lib_menu_element_sidebar_after_menu');

if (!function_exists('law_lib_menu_nav_menu_link_attributes_mobile')) {
    function law_lib_menu_nav_menu_link_attributes_mobile($atts, $item, $args, $depth)
    {
        global $_queried_object_id;
        if ($_queried_object_id
            && ($args->menu??null) === 'mobile-menu'
            && $item instanceof WP_Post
            && isset($item->classes, $item->object_id)
        ) {
            $query_object_id = (int) $_queried_object_id;
            $object_id = (int) $item->object_id;
            if ($query_object_id === $object_id) {
                $item->current = true;
                $item->classes[] = 'current-menu-item';
                if ($item->object === 'page') {
                    $item->classes[] = 'current_page_item';
                }
            }
        }

        if ($depth != 0 || ($args->menu??null) !== 'mobile-menu'
            || (($atts['href']??null) != '#search-section' && ($atts['data-target']??null) != '#search-section')
        ) {
            return $atts;
        }
        $atts['on'] = 'tap:search-section.toggleVisibility, AMP.setState({searchMobileShown: !searchMobileShown})';
        return $atts;
    }
}
add_filter( 'nav_menu_link_attributes', 'law_lib_menu_nav_menu_link_attributes_mobile', 10, 4);
//if (!function_exists('law_lib_menu_wp_nav_menu_container_allowedtags')) {
//    function law_lib_menu_wp_nav_menu_container_allowedtags($tags)
//    {
//        $tags[] = 'amp-mega-menu';
//        return $tags;
//    }
//}
//
//add_filter( 'wp_nav_menu_container_allowedtags', 'law_lib_menu_wp_nav_menu_container_allowedtags' );
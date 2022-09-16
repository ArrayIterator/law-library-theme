<?php
if (!class_exists('Walker_Nav_Menu')) {
    return;
}

if (!class_exists('Law_Lib_Mega_Menu_Walker')) {
    class Law_Lib_Mega_Menu_Walker extends Walker_Nav_Menu
    {
        protected static $called_menu = 0;
        protected $heading_menu_called = 0;

        public function __construct()
        {
            self::$called_menu++;
        }

        public function start_lvl(&$output, $depth = 0, $args = null)
        {
            if ($this->has_children) {
                $output .= "<div role=\"dialog\" class=\"sub-menu-div\">\n";
            }
            parent::start_lvl($output, $depth, $args);
        }

        public function end_lvl(&$output, $depth = 0, $args = null)
        {
            parent::end_lvl($output, $depth, $args);
            if ( ! $this->has_children) {
                $output .= "</div>\n<!-- [role=dialog] -->";
            }
        }
        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
        {
            if ( ! isset($item->title)) {
                $item->title = isset($item->post_title) ? $item->post_title : '';
            }
            if ( ! isset($item->url) && isset($item->ID)) {
                $item->url = get_permalink($item->ID);
            }

            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ($depth) ? str_repeat($t, $depth) : '';

            $classes   = empty($item->classes) ? [] : (array)$item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            /**
             * Filters the arguments for a single nav menu item.
             *
             * @param stdClass $args An object of wp_nav_menu() arguments.
             * @param WP_Post $item Menu item data object.
             * @param int $depth Depth of menu item. Used for padding.
             *
             * @since 4.4.0
             *
             */
            $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

            /**
             * Filters the CSS classes applied to a menu item's list item element.
             *
             * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
             * @param WP_Post $item The current menu item.
             * @param stdClass $args An object of wp_nav_menu() arguments.
             * @param int $depth Depth of menu item. Used for padding.
             *
             * @since 3.0.0
             * @since 4.1.0 The `$depth` parameter was added.
             *
             */
            $class_names = implode(' ',
                apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            /**
             * Filters the ID applied to a menu item's list item element.
             *
             * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
             * @param WP_Post $item The current menu item.
             * @param stdClass $args An object of wp_nav_menu() arguments.
             * @param int $depth Depth of menu item. Used for padding.
             *
             * @since 3.0.1
             * @since 4.1.0 The `$depth` parameter was added.
             *
             */
            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li' . $id . $class_names . '>';

            $atts           = [];
            $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
            $atts['target'] = ! empty($item->target) ? $item->target : '';
            if ('_blank' === $item->target && empty($item->xfn)) {
                $atts['rel'] = 'noopener';
            } else {
                $atts['rel'] = $item->xfn;
            }
            $atts['href']         = ! empty($item->url) ? $item->url : '';
            $atts['aria-current'] = $item->current ? 'page' : '';

            /**
             * Filters the HTML attributes applied to a menu item's anchor element.
             *
             * @param array $atts {
             *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
             *
             * @type string $title Title attribute.
             * @type string $target Target attribute.
             * @type string $rel The rel attribute.
             * @type string $href The href attribute.
             * @type string $aria-current The aria-current attribute.
             * }
             *
             * @param WP_Post $item The current menu item.
             * @param stdClass $args An object of wp_nav_menu() arguments.
             * @param int $depth Depth of menu item. Used for padding.
             *
             * @since 3.6.0
             * @since 4.1.0 The `$depth` parameter was added.
             *
             */
            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (is_scalar($value) && '' !== $value && false !== $value) {
                    $value      = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            /** This filter is documented in wp-includes/post-template.php */
            $title = apply_filters('the_title', $item->title, $item->ID);

            /**
             * Filters a menu item's title.
             *
             * @param string $title The menu item's title.
             * @param WP_Post $item The current menu item.
             * @param stdClass $args An object of wp_nav_menu() arguments.
             * @param int $depth Depth of menu item. Used for padding.
             *
             * @since 4.4.0
             *
             */
            $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);
            $use_submenu = $depth === 0 && $this->has_children;
            $item_output = '';
            if ($use_submenu) {
                $item_output .= "\n<span role=\"button\" aria-label=\"toggle\" class=\"navigation-toggle\" data-depth=\"$depth\">\n";
            }
            $item_output .= $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . $title . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
            if ($use_submenu) {
                $item_output .= "</span>\n";
            }

            /**
             * Filters a menu item's starting output.
             *
             * The menu item's starting output only includes `$args->before`, the opening `<a>`,
             * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
             * no filter for modifying the opening and closing `<li>` for a menu item.
             *
             * @param string $item_output The menu item's starting HTML output.
             * @param WP_Post $item Menu item data object.
             * @param int $depth Depth of menu item. Used for padding.
             * @param stdClass $args An object of wp_nav_menu() arguments.
             *
             * @since 3.0.0
             *
             */
            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }
}

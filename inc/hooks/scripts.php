<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_scripts_register')) {
    function law_lib_scripts_register()
    {
        // CSS
        wp_register_style('law-lib-root', get_theme_file_uri('assets/css/root.css'));
        wp_register_style('law-lib-base', get_theme_file_uri('assets/css/base.css'));
        wp_register_style('law-lib-editor', get_theme_file_uri('assets/css/editor.css'));
        wp_register_style('law-lib-layouts', get_theme_file_uri('assets/css/layouts.css'), []);
        wp_register_style('law-lib-repaint', get_theme_file_uri('assets/css/repaint.css'), ['law-lib-layouts']);
        wp_register_style('law-lib-blocks', get_theme_file_uri('assets/css/blocks.css'), ['law-lib-repaint']);
         wp_register_style('law-lib-fontello', get_theme_file_uri('assets/fontello/css/fontello.css'));
        wp_register_style(
            'law-lib-css-admin',
            get_theme_file_uri( '/assets/css/admin.css' ),
            [ 'law-lib-fontello']
//            [ 'dashicons']
        );
        wp_register_script('law-lib-js-admin', get_theme_file_uri( '/assets/js/admin.js' ), [ 'jquery', 'wp-color-picker' ], null);
        // SELECT 2
        wp_register_script('law-lib-js-select2', get_theme_file_uri( '/assets/select2/select2.min.js' ), ['jquery'], null);
        wp_register_style('law-lib-select2', get_theme_file_uri( '/assets/select2/select2.min.css' ), [], null);
        // customizer backend
        wp_register_script('law-lib-customizer-backend', get_theme_file_uri( '/assets/js/customize.js' ), ['jquery', 'law-lib-js-select2'], null, true);
    }
}

add_action('init', 'law_lib_scripts_register');

if (!function_exists('law_lib_scripts_enqueue_scripts')) {
    function law_lib_scripts_enqueue_scripts()
    {
        wp_enqueue_style('law-lib-fontello');
        // wp_enqueue_style('dashicons');
        wp_enqueue_style('law-lib-root');
        wp_enqueue_style('law-lib-base');
        wp_enqueue_style('law-lib-layouts');
        wp_enqueue_style('law-lib-repaint');
        wp_enqueue_style('law-lib-blocks');
    }
}

add_action('wp_enqueue_scripts', 'law_lib_scripts_enqueue_scripts');

if (!function_exists('law_lib_scripts_admin_enqueue_scripts')) {
    function law_lib_scripts_admin_enqueue_scripts()
    {
        wp_enqueue_style('law-lib-fontello');
        // wp_enqueue_style('dashicons');
        wp_enqueue_style('law-lib-root');
        wp_enqueue_style('law-lib-css-admin');
        global $pagenow;
        if (in_array($pagenow, ['widgets.php', 'post.php',  'post-new.php', 'edit.php'])) {
            wp_enqueue_style('law-lib-blocks');
        }

        if (doing_action('enqueue_block_editor_assets')) {
            wp_enqueue_style('law-lib-editor');
        }
        wp_enqueue_style('law-lib-select2');
        wp_enqueue_script('law-lib-js-select2');
        wp_enqueue_script('law-lib-js-admin');
    }
}

add_action('admin_enqueue_scripts', 'law_lib_scripts_admin_enqueue_scripts');
add_action('enqueue_block_editor_assets', 'law_lib_scripts_admin_enqueue_scripts');

/**
 * Customizer
 */
if (!function_exists('law_lib_scripts_customize_controls_enqueue_scripts')) {
    function law_lib_scripts_customize_controls_enqueue_scripts()
    {
        wp_enqueue_style('law-lib-select2');
        wp_enqueue_script('law-lib-js-select2');
        wp_enqueue_script('law-lib-customizer-backend');
    }
}

add_action('customize_controls_enqueue_scripts', 'law_lib_scripts_customize_controls_enqueue_scripts');

function law_lib_scripts_customize_preview_init()
{
    wp_enqueue_script(
        'law-lib-js-customize-preview',
        get_theme_file_uri('/assets/js/customize-preview.js'),
        [
            'customize-preview',
            'customize-selective-refresh',
            'jquery',
        ],
        wp_get_theme()->get( 'Version' ),
        true
    );
}

add_action( 'customize_preview_init', 'law_lib_scripts_customize_preview_init' );

if (!function_exists('law_lib_scripts_custom_main_css')) {
    function law_lib_scripts_custom_main_css($css)
    {
        $css .= ":root {";
        $data = law_lib_component_option('layout');
        if (is_array($data) && !empty($data)) {
            $to_get = [
                'link-color',
                'link-hover-color',
                'link-focus-color',
                'primary-color',
                'primary-darker-color',
                'primary-light-color',
                'primary-text-color',
                'primary-text-hover-color',
                'primary-link-color',
                'primary-link-hover-color',
                'primary-link-focus-color',
                'meta-link-color',
                'meta-link-hover-color',
                'footer-background-color',
                'footer-text-color',
                'footer-link-color',
                'footer-link-hover-color',
                'footer-link-focus-color',
            ];
            foreach ($to_get as $item) {
                $name = $item;
                $item = $data[$item]??null;
                if (! is_string($item)) {
                    continue;
                }
                $item = trim($item);
                $item = trim($item, '#');
                if (!preg_match('~^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$~', $item)) {
                    continue;
                }
                $css .= "--{$name}:#{$item};";
            }
        }
        $css .= "}";
        return $css;
    }
}

add_filter( 'wp_get_custom_css', 'law_lib_scripts_custom_main_css');

/*!
 * -----------------------------------------------------------------
 * FALLBACK SCRIPTS
 */
if ( ! function_exists( 'law_lib_scripts_facebook_comments' ) ) {
    function law_lib_scripts_facebook_comments($post_id = '') {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $use_fb = law_lib_component_option( 'misc', 'enable_comment' );
        $use_fb = ($use_fb === 'facebook' && is_single($post_id) || is_page($post_id))
            || $use_fb === 'post_facebook' && is_single($post_id)
            || $use_fb === 'page_facebook' && is_page($post_id);
        $us_fb = false;
        if ( $use_fb ) {
            $id     = law_lib_component_option( 'misc', 'facebook_comment_app_id' );
            $id     = is_string( $id ) ? trim( $id ) : $id;
            $id     = is_numeric( $id ) ? (string) $id : null;
            $id     = strlen( $id ) > 5 ? $id : null;
            if ( $id || law_lib_logic_is_amp()) {
                $comment_count = absint( law_lib_component_option( 'misc', 'facebook_comment_count' ) );
                $comment_count = $comment_count < 1 ? 1 : $comment_count;
                $us_fb         = [
                    'id'    => $id,
                    'count' => $comment_count,
                ];
            }
        }

        return $us_fb;
    }
}

if ( ! function_exists( 'law_lib_scripts_is_render_comments' ) ) {
    function law_lib_scripts_is_render_comments($post_id = ''): bool
    {
        return (is_page($post_id) || is_single($post_id)) && comments_open($post_id);
    }
}

if (!function_exists('law_lib_scripts_is_comment_open')) {
    function law_lib_scripts_is_comment_open($open, $post_id)
    {
        $post = get_post($post_id);
        $status = law_lib_component_option( 'misc', 'enable_comment' );
        $id     = law_lib_component_option( 'misc', 'facebook_comment_app_id' );
        $id     = is_string( $id ) ? trim( $id ) : $id;
        $id     = is_numeric( $id ) ? (string) $id : null;
        $id     = strlen( $id ) > 5 ? $id : null;
        switch($status) {
            case 'disable':
                return false;
            case 'facebook':
                return $id ? $open : false;
            case 'post_facebook':
                return $id && is_single($post_id) ? $open : false;
            case 'page_facebook':
                return $id && is_page($post_id) ? $open : false;
        }
        return $open;
    }
}
add_action('comments_open', 'law_lib_scripts_is_comment_open', 10, 2);
global $wp_filter;
unset($wp_filter['comments_open']->callbacks[10]['_close_comments_for_old_post']);

if ( ! function_exists( 'law_lib_scripts_render_facebook_comments' ) ) {
    function law_lib_scripts_render_facebook_comments() {
        if ( ! doing_action( 'wp_footer' ) ) {
            return;
        }

        static $rendered = null;
        if ( $rendered) {
            return;
        }
        $used = law_lib_scripts_facebook_comments();
        if (!$used) {
            return;
        }
        $rendered = true;
        if (! law_lib_scripts_is_render_comments()) {
            return;
        }
        ?>
        <script>
            (function () {
                window.addEventListener('DOMContentLoaded', function () {
                    let element = document.querySelector('.fb-comments');
                    if (!element) {
                        return;
                    }
                    let render = false,
                        observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (render || !entry.isIntersecting) {
                                    return;
                                }
                                render = true;
                                observer.unobserve(element);
                                let script = document.createElement('script');
                                script.crossOrigin = "anonymous";
                                script.defer = true;
                                script.src = "https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v12.0&appId=<?= $used['id']; ?>&autoLogAppEvents=1";
                                document.body.appendChild(script);
                            });
                        }, {
                            root: null,
                            threshold: .5
                        });
                    observer.observe( element );
                });
            })();
        </script>
        <div id="fb-root"></div>
        <?php
    }
}
add_action( 'wp_footer', 'law_lib_scripts_render_facebook_comments' );

/*!
 * -----------------------------------------------------------------
 * MISC
 */
if ( ! function_exists('law_lib_scripts_custom_post_views_popular') ) {
    function law_lib_scripts_custom_post_views_popular() {
        remove_action( 'wp_footer', __FUNCTION__ );

        if (law_lib_component_option('misc', 'disable_tracking') === 'yes') {
            return;
        }

        if (!function_exists('is_plugin_active')
            && file_exists(ABSPATH . '/wp-admin/includes/plugin.php')
        ) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $is_active = ! function_exists( 'is_plugin_active' )
                     || ! is_plugin_active( 'jetpack' )
                     || ! function_exists( 'stats_get_from_restapi' );
        $id        = null;
        if ( $is_active ) :
            $id        = get_the_ID();
            $is_active = is_single() && is_numeric( $id ) && $id > 0;// && !current_user_can('publish_posts');
        endif;

        if ( !$id || ! $is_active ) {
            return;
        }
        if (current_user_can('edit_posts')) {
            return;
        }
        $_args = [
            'pid' => $id,
            'hash' => hash_hmac( 'sha1', $id, SECURE_AUTH_SALT . SECURE_AUTH_KEY . date('Y-m'))
        ];
        $_rest_url = get_rest_url(null, 'law-lib/views-set');
        $_rest_url = add_query_arg($_args, $_rest_url);
        ?>
        <iframe class="hidden hide zero-size" width="0" height="0" src="<?= esc_attr($_rest_url);?>"></iframe>
        <?php
    }
}

add_action( 'wp_footer', 'law_lib_scripts_custom_post_views_popular', 20 );

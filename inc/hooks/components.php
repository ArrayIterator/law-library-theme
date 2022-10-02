<?php

use AmpProject\Dom\Document;
use AmpProject\Dom\Element;

if (!defined('ABSPATH')) :
    return;
endif;

// add_action('amp_enable_optimizer', '__return_true');
if (!function_exists('law_lib_components_wp_body_open')) {
    function law_lib_components_wp_body_open()
    {
        $src = esc_url(get_theme_file_uri('/assets/js/theme.js'));
        echo "<amp-script id='global-script' src='$src'>";
        global $wp;
        $meta = [
            'searchMobileShown' => false,
            'searchBarShown' => false,
            "searchTotalItems" => 0,
            "searchCurrentCount" => 0,
            "searchHasNext" => false,
            "searchItems" => [],
            "searchDone" => false,
            "searchFirstProcessing" => true,
            "searchQuery" => null,
            "searchHiddenList" => true,
            "searchResponseError" => false,
            "searchErrorMessage" => false,
            "searchQuerySpaceCheck" => null,
        ];
        $meta_data = apply_filters('law_lib_components_states', $meta);
        // default is not replace-able
        $meta = array_merge($meta_data, $meta);

        echo '<amp-state id="state-document-global">';
        echo '<script type="application/json">' . json_encode($meta, JSON_UNESCAPED_SLASHES).'</script>';
        echo '</amp-state>';
    }
}
add_action('wp_body_open', 'law_lib_components_wp_body_open', PHP_INT_MIN);

if (!function_exists('law_lib_components_footer_close')) {
    function law_lib_components_footer_close()
    {
        echo "</amp-script>";
    }
}
add_action('wp_footer', 'law_lib_components_footer_close', PHP_INT_MAX);

/* DOM */
if (!function_exists('law_lib_components_buffer')) {
    function law_lib_components_buffer($buffer)
    {
        return str_replace('<title>', '<title [text]="documentTitle">', $buffer);
    }
}
if (!function_exists('law_lib_components_wp')) {
    function law_lib_components_wp()
    {
        if (!did_action('wp')) {
            return;
        }

        remove_action('wp', __FUNCTION__);
        // ob_start('law_lib_components_buffer');
    }
}
add_action('wp', 'law_lib_components_wp');

if (!function_exists('law_lib_components_finalize_dom')) {
    /**
     * @param $dom Document
     *
     * @return Document
     */
    function law_lib_components_finalize_dom(Document $dom) : Document
    {
        return $dom;
        /*
        $title = $dom->head->getElementsByTagName(Tag::TITLE)->item(0);
        if (!$title) {
            return $dom;
        }
        $title->parentNode->removeChild($title);
        $meta = $dom->head->getElementsByTagName(Tag::META);
        if ($meta->length) {
            $meta = $meta->item($meta->length - 1);
            $dom->head->insertBefore($title, $meta->nextSibling);
        }
        */

        /**
         * @var Element $item
         */
        /*
        foreach ($dom->head->getElementsByTagName(Tag::SCRIPT) as $item) {
            if ($item->hasAttribute('id') && $item->getAttribute('id') === 'law-lib-microdata') {
                $value = $item->nodeValue;
                $value = json_decode($value, true);
            }
        }

        return $dom;
        */

        /**
         * @var Element $item
         */
        /*
        foreach ($dom->body->getElementsByTagName(AmpState::ID) as $item) {
            $id = $item->getAttribute('id');
            if ($id === 'state-document-global') {
                $item->parentNode->removeChild($item);
                $dom->head->insertBefore($item, $title);
                break;
            }
        }
        return $dom;
        */
    }
}

add_action('amp_finalize_dom', 'law_lib_components_finalize_dom');


if (!function_exists('law_lib_components_wp_get_attachment_image_src')) {
    function law_lib_components_wp_get_attachment_image_src($image, $attachment_id, $size)
    {
        return $image?:law_lib_component_get_default_image($size);
    }
}
add_filter('wp_get_attachment_image_src', 'law_lib_components_wp_get_attachment_image_src', 10, 3);

if (!function_exists('law_lib_components_before_container')) {
    function law_lib_components_before_container($_post_id = false)
    {
        if (is_404() || is_home() && !is_paged() || is_front_page()) {
            return;
        }

        $globals = law_lib_component_option('global');
        $globals = !is_array($globals) ? [] : $globals;
        $show_breadcrumbs = false;
        if ($_post_id) {
            if (is_single($_post_id)) {
                $show_breadcrumbs = ($globals['show_breadcrumb_post']??'yes') !== 'no';
            } elseif (is_page($_post_id)) {
                $show_breadcrumbs = ($globals['show_breadcrumb_page']??'yes') !== 'no';
            }
        } else {
            $show_breadcrumbs = ($globals['show_breadcrumb_archive']??'yes') !== 'no';
        }
        if ($show_breadcrumbs) {
            $args = [];
            if ($_post_id) {
                $args = ['post_id' => $_post_id];
            }
            get_template_part('templates/partials/breadcrumbs', null, $args);
        }
        if (!$_post_id) {
            get_template_part('templates/partials/archive-header', null, $args);
        }
    }
}

add_action('before_loop', 'law_lib_components_before_container', 1);

if (!function_exists('law_lib_components_after_loop')) {
    function law_lib_components_after_loop()
    {
        global $wp_query;
        $is_home = !empty($wp_query->has_front_page_data)
            && law_lib_logic_enable_blog_filtering();
        if (!$is_home && !is_archive() && !is_search() && !is_home()) {
            return;
        }
        $_the_mods = law_lib_component_option(
            $is_home
                ? 'homepage'
                : (is_search() ? 'search' : 'archive')
        );

        if (!$is_home && ($_the_mods['ajax_pagination']??'yes') === 'no') {
            the_posts_pagination(
                [
                    'before_page_number' => '',
                    'mid_size'           => 1,
                    'end_size'           => 3,
                    'start_size'         => 1,
                    'prev_text'          => sprintf(
                        '%s <span class="nav-prev-text">%s</span>',
                        is_rtl() ? '<i class="ic-caret-right"></i>' : '<i class="ic-caret-left"></i>',
                        wp_kses(
                            __( 'Previous', 'law-lib' ),
                            [
                                'span' => [
                                    'class' => [],
                                ],
                            ]
                        )
                    ),
                    'next_text'          => sprintf(
                        '<span class="nav-next-text">%s</span> %s',
                        wp_kses(
                            __( 'Next', 'law-lib' ),
                            [
                                'span' => [
                                    'class' => [],
                                ],
                            ]
                        ),
                        is_rtl() ? '<i class="ic-caret-left"></i>' : '<i class="ic-caret-right"></i>'
                    ),
                ]
            );
            return;
        }
        $_load_more_text_default = __('Load More', 'law-lib');
        $_loading_text_default =  __('Loading ...', 'law-lib');

        $_load_more_text = $_the_mods['load_more_text']??$_load_more_text_default;
        $_load_more_text = !is_string($_load_more_text) ? $_load_more_text_default : $_load_more_text;

        $_loading_text = $_the_mods['loading_text']??$_loading_text_default;
        $_loading_text = !is_string($_loading_text) ? $_loading_text_default : $_loading_text;

        $_load_more_text = apply_filters('law_lib_loop_load_more_text', $_load_more_text);
        $_loading_text = apply_filters('law_lib_loop_load_more_loading_text', $_loading_text);

        $_args = [
            'amp-request' => $is_home ? 'blog' : 'archive',
        ];
        global $wp_query;
        $found = true;
        if ($wp_query->is_search()) {
            $_args['search'] = get_search_query(false);
        } elseif ($wp_query->is_tag()) {
            $__tag = get_term_by('slug', $wp_query->get('tag'),'post_tag');
            if ($__tag && isset($__tag->term_id)) {
                $_args['tags'] = $__tag->term_id;
            } else {
                $found = false;
            }
        } elseif ($wp_query->is_category()) {
            $_args['categories'] = $wp_query->get('cat');
            $_args['include_children'] = true;
        } elseif ($wp_query->is_author()) {
            $_args['author'] = $wp_query->get('author');
        } elseif ($wp_query->is_year() || $wp_query->is_day() || $wp_query->is_month()) {
            $day = $wp_query->get('day');
            $day = !is_numeric($day) ? 0 : absint($day);
            $day = $day < 10 ? "0{$day}" : "$day";
            $month = $wp_query->get('monthnum');
            $month =!is_numeric($month) ? 0 : absint($month);
            $month = $month < 10 ? "0{$month}" : "$month";
            $year = $wp_query->get('year');
            $_args['modified_after'] = "$year-$month-$day 00:00:00";
        }

        $_rest_url = get_rest_url(null, 'law-lib/posts');
        $_rest_url = add_query_arg($_args, $_rest_url);
        $page = $wp_query->get('page')??1;
        $page = $page < 1 ? 1 : $page;
        $post_count = $wp_query->post_count * $page;
        if ($found && $post_count < $wp_query->found_posts) :
    ?>
        <div class="looping-load-more-wrapper">
            <button class="button-loop-load-more" data-offset='<?= $post_count;?>' data-loop='true' data-rest-url='<?= esc_attr($_rest_url);?>' data-button="load-more" role="button" data-loading="<?= esc_attr($_loading_text);?>" aria-label="<?php esc_attr_e('Load More', 'law-lib');?>">
                <?= $_load_more_text;?>
            </button>
        </div>
        <?php
    endif;
    }
}

add_action('after_loop', 'law_lib_components_after_loop');

/*!
 * -----------------------------------------------------------------
 * COMMENTS
 */
if (!function_exists('law_lib_components_render_facebook_meta_head')) {
    function law_lib_components_render_facebook_meta_head()
    {
        static $inserted = false;
        if ($inserted) {
            return;
        }
        $insert     = law_lib_component_option( 'misc', 'facebook_insert_to_meta', 'no' );
        if ($insert === 'yes') {
            $id     = law_lib_component_option( 'misc', 'facebook_comment_app_id' );
            $id     = is_string( $id ) ? trim( $id ) : $id;
            $id     = is_numeric( $id ) ? (string) $id : null;
            $id     = strlen( $id ) > 5 ? $id : null;
            if ($id) {
                $inserted = true;
                ?>
                    <meta property="fb:app_id" content="<?= $id;?>"/>
                <?php
            }
        }
    }
}
add_action('wp_head', 'law_lib_components_render_facebook_meta_head');

if ( ! function_exists( 'law_lib_components_render_comments_template' ) ) {
    function law_lib_components_render_comments_template() {
        $post_id = get_the_ID();
        if ( ! $post_id || get_post_status( $post_id ) !== 'publish'
            || (!is_single($post_id))
        ) {
            return;
        }

        remove_action( 'after_main', __FUNCTION__ );

        $use_facebook = law_lib_scripts_facebook_comments();
        // If comments are open or there is at least one comment, load up the comment template.
        do_action( 'before_comment_section', $post_id, $use_facebook );
        if ( ! law_lib_scripts_is_render_comments($post_id) && ! $use_facebook ) {
            return;
        }
        ?>
        <div id="comment-section">
            <div class="comment-section-wrapper">
                <?php
                if (! $use_facebook ) {
                    ?>
                    <?php comments_template(); ?>
                <?php } else {
                    if (law_lib_logic_is_amp()) {
                        $permalink = esc_attr(get_permalink($post_id));
                        ?>
                        <amp-facebook-comments
                                width="600"
                                height="400"
                                layout="responsive"
                                data-numposts="<?= $use_facebook['count'];?>"
                                data-href="<?= $permalink;?>"
                        ></amp-facebook-comments>
                        <?php
                    } else {
                ?>
                    <div class="fb-comments" data-href="<?= esc_url( get_permalink( $post_id ) ); ?>" data-width="100%"
                         data-numposts="<?= $use_facebook['count']; ?>"></div>
                <?php
                    }
                } ?>
            </div>
            <!-- .comment-section-wrapper -->
        </div>
        <!-- #comment-section -->
        <?php
        do_action( 'after_comment_section', $post_id, $use_facebook );
    }
}
add_action( 'after_loop', 'law_lib_components_render_comments_template' );

/*
 * SINGLE POST
 */

if (!function_exists('law_lib_components_after_entry_header')) {
    function law_lib_components_after_entry_header($post_id)
    {
        $post_id = law_lib_metadata_determine_post_id($post_id);
        if (!$post_id || !is_single($post_id)) {
            return;
        }
        if (law_lib_component_option('post', 'show_meta') !== 'yes') {
            return;
        }
        get_template_part(
            'templates/partials/entries/entry-meta',
            get_post_format($post_id),
            ['post_id' => $post_id]
        );
    }
}
add_action('after_entry_header', 'law_lib_components_after_entry_header', 1);

if (!function_exists('law_lib_components_after_content_sidebar')) {
    function law_lib_components_after_content_sidebar($post_id)
    {
        if (!is_single($post_id)) {
            return;
        }

        $_sidebar_name = 'below-post';
        $_is_has_sidebar = is_active_sidebar($_sidebar_name);
        ?>
        <?php if ($_is_has_sidebar) : ?>
        <?php do_action('before_sidebar_section', $_sidebar_name);?>
        <div class="sidebar-section below-post-sidebar">
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
    <?php
    }
}
add_action('after_entry_content', 'law_lib_components_after_content_sidebar', 2);

if (!function_exists('law_lib_components_after_entry_tags')) {
    function law_lib_components_after_entry_tags($post_id)
    {
        $post_id = law_lib_metadata_determine_post_id($post_id);
        if (!$post_id || !is_single($post_id)) {
            return;
        }
        if (law_lib_component_option('post', 'show_tags') !== 'yes') {
            return;
        }
        get_template_part(
                'templates/partials/entries/entry-tags',
                get_post_format($post_id),
                ['post_id' => $post_id]
        );
    }
}
add_action('after_entry_content', 'law_lib_components_after_entry_tags', -1);

if (!function_exists('law_lib_components_after_author_bio')) {
    function law_lib_components_after_author_bio($post_id)
    {
        $post_id = law_lib_metadata_determine_post_id($post_id);
        if (!$post_id || !is_single($post_id)) {
            return;
        }
        if (law_lib_component_option('post', 'show_author_bio') !== 'yes') {
            return;
        }
        get_template_part(
                'templates/partials/entries/entry-author-bio',
                get_post_format($post_id),
                ['post_id' => $post_id]
        );
    }
}
add_action('after_entry_content', 'law_lib_components_after_author_bio', 0);

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! function_exists( 'law_lib_components_widget_register' ) ) {
	function law_lib_components_widget_register() {
        if (class_exists('Law_Lib_Widget_SpecialHTML')) {
		    register_widget( Law_Lib_Widget_SpecialHTML ::class );
        }
	}
}
add_action( 'widgets_init', 'law_lib_components_widget_register' );

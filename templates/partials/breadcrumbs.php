<?php
if ( ! defined('ABSPATH')) :
    return;
endif;

$_post_id        = $args['post_id'] ?? get_the_ID();
$_is_search      = is_search() && have_posts();
$is_singular     = $_post_id && is_singular(get_post_type($_post_id));
$_queried_object = get_queried_object();
if (!$_is_search && ! $_queried_object && ! $is_singular) {
    return;
}

$_primary_category = $_post_id ? Law_Lib_Meta_Data::primaryCategory($_post_id) : false;
$_separator        = apply_filters(
    'law_lib_breadcrumbs_separator',
    '<i class="ic-caret-right"></i>',
    $_queried_object
);
$_data = [
    [
        'rel' => 'home',
        'class' => ['home-link'],
        'href' => home_url(),
        'itemprop' => 'item',
        'text' => __('Home', 'law-lib'),
    ]
];
if ($is_singular) {
    $_data[] = [
        'class' => !$_primary_category ? ['page-link'] : ['archive-link'],
        'href' => !$_primary_category ? get_permalink($_post_id) : get_category_link($_primary_category->term_id),
        'itemprop' => 'item',
        'itemtype' => 'https://schema.org/WebPage',
        'text' => $_primary_category ? strip_tags($_primary_category->name) : get_the_title($_post_id),
    ];
} else {
    $_data[] = [
        'class' => ['archive-link'],
        'href' => $_is_search ? get_search_link() : get_category_link($_queried_object->term_id),
        'itemprop' => 'item',
        'itemref' => 'category',
        'text' => strip_tags(Law_Lib_Meta_Query::archiveName()),
    ];
    if ($_is_search) {
        $_data[] = [
            'rel' => 'search',
            'class' => ['archive-link', 'search-link'],
            'href'  => get_search_link(),
            'itemprop' => 'item',
            'itemref' => 'search',
            'itemtype' => 'https://schema.org/SearchResultsPage',
            'text'  => get_search_query(),
        ];
    } else {
        $_data[] = [
            'rel' => 'category',
            'class' => ['archive-link'],
            'href'  => get_category_link($_queried_object->term_id),
            'itemprop' => 'item',
            'itemref'  => 'category',
            'itemtype' => 'https://schema.org/category',
            'text'  => strip_tags($_queried_object->name),
        ];
    }
}

$_globals     = law_lib_component_option('global');
$_globals     = !is_array($_globals) ? [] : $_globals;
$_enable_microdata     = ($_globals['enable_microdata']??'yes') === 'yes';
$_microdata = '';
if ($_enable_microdata) {
    $_microdata = ' itemscope itemtype="https://schema.org/BreadcrumbList"';
    $_current_url = $_is_search ? get_search_link() : (
        $is_singular
            ? get_permalink($_post_id)
            : get_category_link($_queried_object->term_id)
    );
    $_current_url = esc_url("{$_current_url}#breadcrumb");
    $_microdata .= " itemid=\"{$_current_url}\"";
}
$_html = '<ol id="breadcrumb" class="breadcrumb"' . $_microdata . ">\n";
foreach ($_data as $_key => $_item) {
    $_microdata_list = $_enable_microdata ? ' itemscope itemprop="itemListElement" itemtype="https://schema.org/ListItem"' : '';
    $_text           = esc_html($_item['text']);
    $_class_name = $_item['class'];
    unset($_item['text']);
    if (!$_enable_microdata) {
        unset($_item['itemprop'], $_item['itemref'], $_item['itemtype']);
    }
    $_item['class'] = implode(', ', $_class_name);
    $_html          .= "<li{$_microdata_list}>\n";
    $_prop          = $_enable_microdata ? ' item="name"': '';
    $_text   = "<span itemprop='name'>$_text</span>";
    $attr = '';
    foreach ($_item as $_k => $_value) {
        $_value = esc_attr($_value);
        $attr .= " {$_k}=\"$_value\"";
    }
    $_html .= "<a{$attr}>\n{$_text}\n</a>\n";
    if ($_enable_microdata) {
        $_html .= '<meta itemprop="position" content="' . ($_key + 1) . "\">\n";
    }
    $_html .= "</li>\n";
}
$_html .= "</ol>\n";
echo $_html;
unset($_html);

<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_show_date = (law_lib_component_option('search', 'show_date', 'yes')?:'yes') === 'yes';
$_show_category = (law_lib_component_option('search', 'show_category', 'yes')?:'yes') === 'yes';
$_show_thumbnails = (law_lib_component_option('search', 'show_thumbnail', 'yes')?:'yes') === 'yes';
?>
<div class="search-entry-search search-entry-found post-id-{{id}}" data-id="{{id}}">
    <div class="search-entry-thumbnail">
        {{#thumbnail}}
        <a href="{{link}}" class="entry-thumbnail-link">
            <img class="search-entry-thumbnail-image" src="{{attributes.src}}" width="{{attributes.width}}" height="{{attributes.height}}" alt="{{attributes.alt}}" srcset="{{attributes.srcset}}" loading="lazy" sizes="{{attributes.sizes}}">
        </a>
        {{/thumbnail}}
    </div>
    <h2 class="search-entry-title">
        <a href="{{link}}" class="search-entry-title-link entry-title-link">{{title.rendered}}</a>
    </h2>
    <?php if ($_show_date || $_show_category) :?>
    <div class="search-entry-meta">
        <?php if ($_show_date) : ?>
        <time datetime="{{date_gmt}}" class="search-entry-published-date">{{date_format}}</time>
        <?php endif;?>
        <?php if ($_show_category) : ?>
        <div class="search-entry-category" data-category-id="{{primary_category.term_id}}">
            <a class="primary-category-link" href="{{primary_category.link}}">
                {{primary_category.name}}
            </a>
        </div>
        <?php endif;?>
    </div>
    <?php endif;?>
</div>

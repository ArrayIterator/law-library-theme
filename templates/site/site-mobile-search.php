<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_meta_input = [
    'id',
    'title',
    'date',
    'date_gmt',
    'date_gmt_format',
    'date',
    'date_format',
    'link',
    'category_names',
    'tag_names',
    'author',
    'author_name',
    'excerpt',
    'primary_category',
    'thumbnail'
];


$_search_options = law_lib_component_option('search');
$_search_options = !is_array($_search_options) ? [] : $_search_options;
$_search_per_page = $_search_options['per_page_first']??10;
$_search_per_page = !is_numeric($_search_per_page) ? 10 : absint($_search_per_page);
$_search_per_page = $_search_per_page < 2 ? 2 : $_search_per_page;
$_search_per_page = $_search_per_page > 50 ? 50 : $_search_per_page;

$_args = [
    '_fields' => $_meta_input,
    'post_type' => 'post',
    // 'per_page' => $_search_per_page,
    'orderby' => 'relevance',
    'amp-request' => 'search',
];
if ((law_lib_component_option('search', 'show_thumbnail', 'yes')?:'yes') === 'yes') {
    $_args['thumbnail'] = 'true';
}
/*
$_category_options = $_search_options['categories_exclude']??[];
$_category_options = law_lib_component_get_all_category_as_key_name_sanitize($_category_options);

if (!empty($_category_options)) {
    $args['categories_exclude'] = implode(',', $_category_options);
}*/


$_rest_url = get_rest_url(null, 'law-lib/posts');
$_rest_url = add_query_arg($_args, $_rest_url);

$_load_more_text_default = __('Load More', 'law-lib');
$_loading_text_default =  __('Loading ...', 'law-lib');
$_load_more_text = $_search_options['load_more_text']??$_load_more_text_default;
$_load_more_text = !is_string($_load_more_text) ? $_load_more_text_default : $_load_more_text;

$_loading_text = $_search_options['loading_text']??$_loading_text_default;
$_loading_text = !is_string($_loading_text) ? $_loading_text_default : $_loading_text;

$_load_more_text = apply_filters('law_lib_search_load_more_text', $_load_more_text);
$_loading_text = apply_filters('law_lib_search_load_more_loading_text', $_loading_text);

?>
<div id="search-section" class="search-section" hidden [class]="processing ? 'search-section search-processing': 'search-section'">
    <div class="search-section-container">
        <form role="search" class="search-form-section" method="GET" action-xhr="<?= esc_attr($_rest_url);?>" on="submit:AMP.setState({
                searchTotalItems:0,
                searchCurrentCount:0,
                searchHasNext: false,
                searchItems: [],
                searchDone: false,
                searchFirstProcessing: true,
                searchQuery : searchThrottledValue,
                searchProcessing: true,
                searchHiddenList: true,
                searchResponseError: false,
                searchErrorMessage: false
             }), search-section-list.refresh, search-section-list.changeToLayoutContainer, search-result-section.focus;submit-success: AMP.setState({
                searchTotalItems: event.response.item_total,
                searchCurrentCount: event.response.items.length,
                searchHasNext: event.response.items.length < event.response.item_total,
                searchItems: event.response.items,
                searchDone: true,
                searchFirstProcessing: false,
                searchQuery : searchThrottledValue,
                searchProcessing: false,
                searchHiddenList: false,
                searchResponseError: false,
                searchErrorMessage: false
            });submit-error:AMP.setState({
                searchResponseError: true,
                searchProcessing: false,
                searchFirstProcessing: false,
                searchQuerySpaceCheck: false,
                searchErrorMessage: event.response.message ? event.response.message : '<?php esc_attr_e('There was an error while getting data.', 'law-lib');?>'
            }),search-section-list.hide">
            <label for="search-section-input-search" hidden class="screen-reader-text hidden">
                <?php _e('Search', 'law-lib');?>
            </label>
            <div class="input-group">
                <input id="search-section-input-search" maxlength="120" class="form-control" type="search" name="search" value="" placeholder="<?= esc_attr_x('Search The Reference ... ', 'label', 'law-lib');?>" on="input-throttled:AMP.setState({ searchThrottledValue: event.value })">
                <div class="input-group-text">
                    <button type="submit" aria-label="submit" disabled [disabled]="! searchFirstProcessing && searchQuerySpaceCheck && searchQuerySpaceCheck.length > 0 && searchQuery != searchThrottledValue ? false : true" [class]="searchFirstProcessing ? 'search-processing-button' : ''">
                        <i class="ic-search"></i>
                    </button>
                </div>
            </div>
        </form>
        <div id="search-result-section" class="search-result-section">
            <amp-list [hidden]="searchFirstProcessing" id="search-section-list" reset-on-refresh hidden [hidden]="!!searchHiddenList" [src]="searchItems" layout="nodisplay">
                <template type="amp-mustache" id="template-search-section-response-success">
                    {{#.}}

                    <?php get_template_part('templates/components/mustache/search-entry', null, ['rest_url' => $_rest_url]);?>

                    {{/.}}
                </template>
            </amp-list>

            <?php /* PROCESSING */ ?>
            <div id="search-section-submitting" class="search-section-submitting" [hidden]="searchFirstProcessing ? false : true" hidden>
                <?php get_template_part('templates/components/search-submitting', null, ['rest_url' => $_rest_url]);?>
            </div>
            <div id="search-section-notfound" class="search-section-notfound" hidden [hidden]="! searchDone || searchItems.length > 0 ? true : false">
                <?php get_template_part('templates/components/search-empty', null, ['rest_url' => $_rest_url]);?>
            </div>
            <div id="search-section-error" class="search-section-error" hidden [hidden]="! searchResponseError">
                <?php get_template_part('templates/components/search-error', null, ['rest_url' => $_rest_url]);?>
            </div>
            <?php /* END PROCESSING */ ?>

            <form [class]="! hasNextSearch  ? 'hidden' : ''" method="GET" action-xhr="<?= esc_attr($_rest_url);?>" target="_top" on="submit:AMP.setState({searchProcessing: true, searchLoadMoreResponseError: false}),search-result-section.focus;submit-success: AMP.setState({
                searchTotalItems: event.response.item_total,
                searchCurrentCount: event.response.items.length + searchCurrentCount,
                searchHasNext: searchCurrentCount < event.response.item_total,
                searchItems: searchItems.concat(event.response.items),
                searchDone: true,
                searchFirstProcessing: false,
                searchProcessing: false,
                searchHiddenList: false,
                searchResponseError: false,
                searchErrorMessage: false
            });submit-error:AMP.setState({
                searchLoadMoreResponseError: true,
                searchProcessing: false,
                searchQuerySpaceCheck: false
             })">
                <input type="hidden" name="offset" value="<?= $_search_per_page - 1;?>" [value]="searchCurrentCount">
                <input type="hidden" name="search" [value]="searchQuery">
                <div class="looping-load-more-wrapper">
                    <button type="submit" class="search-load-more" [class]="!searchProcessing ? 'search-load-more' : 'search-load-more in-process'" hidden [hidden]="searchTotalItems <= searchCurrentCount || ! searchHasNext ? true : false" [text]="searchProcessing ? '<?=
                        esc_attr($_loading_text);
                    ?>' : '<?= esc_attr($_load_more_text); ?>'">
                        <?= $_load_more_text; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

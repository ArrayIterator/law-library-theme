<?php
if (!defined('ABSPATH')) :
    return;
endif;

global $___search_called_id;
if (!$___search_called_id) {
    $___search_called_id = 0;
}
$is_no_filter = (bool) ($args['no-filter']??false);
$___search_called_id++;
$_aria_label = isset($args['aria_label']) && is_string($args['aria_label'])
    ? esc_attr($args['aria_label'])
    : esc_attr_x('Search Form', 'label', 'law-lib');
$_placeholder = isset($args['placeholder']) && is_string($args['placeholder'])
    ? esc_attr($args['placeholder'])
    : esc_attr_x('Type to search ... ', 'label', 'law-lib');
?>
<form role="search" aria-label="<?= $_aria_label;?>" method="get" class="search-form" action="<?= esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="navigation-search-form-<?= $___search_called_id;?>"><?php _e('Search');?></label>
    <input type="search" id="navigation-search-form-<?= $___search_called_id;?>"
           class="form-control form-control-sm search-field"
           placeholder="<?= $_placeholder; ?>"
           value="<?= get_search_query(); ?>" name="s" maxlength="60">
    <button class="btn btn-sm ic-search" aria-label="submit"></button>
    <?php if ($is_no_filter) : ?>
        <input type="hidden" name="no-filter" value="true" hidden>
    <?php endif;?>
</form>

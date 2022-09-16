<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
<div class="search-entry-search search-entry-error search-entry-search-case">
    <div>
        <div class="search-error-icon search-entry-case-icon"><i class="ic-close"></i></div>
        <div class="search-entry-query" [text]="searchQuery"></div>
        <div class="search-error-text search-entry-case-text" [text]="searchErrorMessage"><?php _e('There was an error while getting data.', 'law-lib');?></div>
    </div>
</div>

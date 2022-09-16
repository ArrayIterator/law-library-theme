<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
<div class="search-entry-search search-entry-not-found search-entry-search-case">
    <div>
        <div class="search-not-found-icon search-entry-case-icon"><i class="ic-close"></i></div>
        <div class="search-entry-query" [text]="searchQuery"></div>
        <div class="search-not-found-text search-entry-case-text">
            <?php _e('Entry Not Found.', 'law-lib');?>
        </div>
    </div>
</div>

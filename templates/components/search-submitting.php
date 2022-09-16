<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
<div class="search-entry-search search-entry-submitting search-entry-search-case">
    <div>
        <div class="search-submitting-icon search-entry-case-icon"><i class="ic-search"></i></div>
        <div class="search-entry-query" [text]="searchQuery"></div>
        <div class="search-submitting-text search-entry-case-text">
            <?php _e('Loading...', 'law-lib');?>
        </div>
    </div>
</div>

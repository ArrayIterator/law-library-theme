<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
$_is_singular = is_single($_post_id);
if (!$_is_singular) {
    return;
}
?>
<?php /* Content Entry Tags */ ?>
<?php do_action('before_tags_entry', $_post_id);?>
<div class="entry-tags tags-list">
    <div class="tag-list-title">
        <?php _e( 'Tags:', 'law-lib' ); ?>
    </div>
    <div class="tag-list-wrapper">
        <?php echo get_the_tag_list( '', '', '', $_post_id ); ?>
    </div>
</div>
<?php do_action('after_tags_entry', $_post_id);?>
<?php /* End Content Entry Tags */ ?>

<?php
if (!defined('ABSPATH')) :
    return;
endif;
$_post_id = $args['post_id']??get_the_ID();
?>

<?php if (law_lib_logic_loop_setting('show_excerpt')) : ?>
    <?php /* Content Entry Content */ ?>
    <?php do_action('before_content_entry', $_post_id);?>
    <div class="entry-content">
        <?php $display_archive_excerpt = apply_filters('display_archive_excerpt', true, $_post_id);?>
        <?php do_action('before_entry_content', $_post_id, $display_archive_excerpt);?>
        <?php if ($display_archive_excerpt) : ?>
            <?php the_excerpt();?>
        <?php endif;?>
        <?php do_action('after_entry_content', $_post_id, $display_archive_excerpt);?>
    </div>
    <?php do_action('after_content_entry', $_post_id);?>
    <?php /* End Content Entry Content */ ?>
<?php endif;?>


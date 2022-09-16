<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
?>

<?php /* Content Entry Footer */ ?>
<?php do_action('before_footer_entry', $_post_id);?>
<div class="entry-footer">
    <?php do_action('before_entry_footer', $_post_id);?>
    <?php do_action('after_entry_footer', $_post_id);?>
</div>
<?php do_action('after_footer_entry', $_post_id);?>
<?php /* Content Entry Footer */ ?>


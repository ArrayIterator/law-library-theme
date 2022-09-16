<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_the_id = get_the_ID();
$_post_id = $args['post_id']??$_the_id;
$_post = $_the_id === $_post_id ? get_the_content() : get_post($_post_id);
?>
<div <?php post_class();?> data-post-id="<?= $_post_id;?>">
    <?php /* Content Entry Content */ ?>
    <?php do_action('before_content_entry', $_post_id);?>
    <div class="entry-content entry-content-home">
        <?php echo apply_filters('the_content', $_post?:'', $_post_id); ?>
    </div>
    <?php do_action('after_content_entry', $_post_id);?>
    <?php /* End Content Entry Content */ ?>
</div>

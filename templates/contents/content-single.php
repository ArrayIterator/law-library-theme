<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
$_permalink = get_permalink($_post_id);
?>
<article <?php post_class('', $_post_id)?> data-post-id="<?= $_post_id;?>">
    <?php /* Content Entry Header */ ?>
    <?php do_action('before_header_entry', $_post_id);?>
    <header class="entry-header singular-header">
        <?php do_action('before_entry_header', $_post_id);?>
        <?php the_title('<h1 class="entry-title">', '</h1>');?>
        <?php do_action('after_entry_header', $_post_id);?>
    </header>
    <?php do_action('after_header_entry', $_post_id);?>
    <?php /* End Content Entry Header */ ?>

    <?php /* Content Entry Content */ ?>
    <?php do_action('before_content_entry', $_post_id);?>
    <div class="entry-content entry-content-singular">
        <?php do_action('before_entry_content', $_post_id, false);?>
        <?php the_content();?>
        <?php do_action('after_entry_content', $_post_id, false);?>
    </div>
    <?php do_action('after_content_entry', $_post_id);?>
    <?php /* End Content Entry Content */ ?>

    <?php /* Content Entry Footer */ ?>
    <?php do_action('before_footer_entry', $_post_id);?>
    <div class="entry-footer">
        <?php do_action('before_entry_footer', $_post_id);?>

        <?php do_action('after_entry_footer', $_post_id);?>
    </div>
    <?php do_action('after_footer_entry', $_post_id);?>
    <?php /* Content Entry Footer */ ?>
</article>

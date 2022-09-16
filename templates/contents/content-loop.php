<?php
if (!defined('ABSPATH')) :
    return;
endif;

global $wp_query;

$_post_id = $args['post_id']??get_the_ID();
if (law_lib_logic_loop_setting('show_thumbnail')) {
    $_thumbnail = Law_Lib_Image::thumbnailImageAttachment($_post_id);
} else {
    $_thumbnail = false;
}

?>
<article <?php post_class($_thumbnail ? 'has-thumbnail' : '', $_post_id); ?> data-post-id="<?= $_post_id;?>">
    <?php /* Content Entry Thumbnail */ ?>
    <?php if ($_thumbnail) :?>
        <?php do_action('before_entry_thumbnail', $_post_id);?>
        <div class="entry-thumbnail">
            <a class="entry-thumbnail-link thumbnail-link" href="<?= esc_url(get_permalink($_post_id));?>"><?= $_thumbnail['html'];?></a>
        </div>
        <?php do_action('after_entry_thumbnail', $_post_id);?>
    <?php endif;?>
    <?php /* End Content Entry Thumbnail */ ?>
    <?php get_template_part('templates/partials/entries/entry-header', get_post_format(), ['post_id' => $_post_id]);?>
    <?php get_template_part('templates/partials/entries/entry-meta', get_post_format(), ['post_id' => $_post_id]);?>
    <?php get_template_part('templates/partials/entries/entry-excerpt', get_post_format(), ['post_id' => $_post_id]);?>
    <?php get_template_part('templates/partials/entries/entry-footer', get_post_format(), ['post_id' => $_post_id]);?>
</article>

<?php
if (!defined('ABSPATH')) :
    return;
endif;
$_post_id = $args['post_id']??get_the_ID();
?>
<?php /* Content Entry Content */ ?>
<?php do_action('before_content_entry', $_post_id);?>
<div class="entry-content">
    <?php do_action('before_entry_content', $_post_id, false);?>
    <?php if (is_404() || $_post_id === false) : ?>
        <?php if (is_search()) : ?>
            <p><?php esc_html_e(
                    'Sorry, but nothing matched your search terms. Please try again with some different keywords.',
                    'law-lib'
                ); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php esc_html_e(
                    'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.',
                    'law-lib'
                ); ?></p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    <?php else : ?>
        <?php the_content();?>
    <?php endif;?>

    <?php do_action('after_entry_content', $_post_id, false);?>
</div>
<?php do_action('after_content_entry', $_post_id);?>
<?php /* End Content Entry Content */ ?>


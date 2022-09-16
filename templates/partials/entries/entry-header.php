<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
$_thumbnail = law_lib_logic_loop_setting('show_thumbnail') ?
    Law_Lib_Image::thumbnailImageAttachment(
        $_post_id
    ) : null;
$_permalink = get_permalink($_post_id);
$_is_singular = is_singular($_post_id);
?>
<?php /* Content Entry Header */ ?>
<?php do_action('before_header_entry', $_post_id);?>
<header class="entry-header<?= $_is_singular ? ' singular-header' : '';?>">
    <?php do_action('before_entry_header', $_post_id);?>
    <?php if (is_404()) : ?>
        <h1 class="entry-title">
            <?php _e('404 Page Not Found', 'law-lib'); ?>
        </h1>
    <?php elseif ($_is_singular) : ?>
        <?php the_title('<h1 class="entry-title">', '</h1>');?>
    <?php else : ?>
        <?php printf(
            '<h2 class="entry-title"><a href="%s" class="entry-title-link" rel="permalink">%s</a></h2>',
            esc_url( $_permalink ),
            get_the_title( $_post_id )
        ); ?>
    <?php endif;?>

    <?php do_action('after_entry_header', $_post_id);?>
</header>
<?php do_action('after_header_entry', $_post_id);?>
<?php /* End Content Entry Header */ ?>

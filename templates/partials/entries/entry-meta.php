<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
if (!$_post_id) {
    return;
}

$_show_date = law_lib_logic_loop_setting('show_date');
$_show_author = law_lib_logic_loop_setting('show_author');
$_show_category = law_lib_logic_loop_setting('show_category');

$_primary_category = $_show_category ? Law_Lib_Meta_Data::primaryCategory($_post_id) : false;
if ($_show_date || $_primary_category || $_show_author) :
    $_permalink = get_permalink($_post_id);
    $_post        = get_post( $_post_id );
    $_local       = get_post_datetime( $_post_id );
    $_date_format = get_option( 'date_format' );
    $_time_format = get_option( 'time_format' );
    $_user        = get_userdata( $_post->post_author );
    $_time        = sprintf(
        '<time datetime="%1$s" class="published-date">%2$s %3$s %4$s</time>',
        esc_attr( get_the_date( 'c', $_post_id ) ),
        esc_html( wp_date( $_date_format, $_local->getTimestamp(), $_local->getTimezone() ) ),
        apply_filters( 'law_lib_date_hour_separator', '|' ),
        esc_html( $_local->format( $_time_format ) )
    );
    ?>
    <?php /* Content Entry Meta */ ?>
    <div class="entry-meta">
        <?php if ($_primary_category) : ?>
            <div class="category entry-meta-category">
                <a href="<?= esc_url(get_category_link( $_primary_category )); ?>" class="entry-category-link"><?= esc_html($_primary_category->name); ?></a>
            </div>
        <?php endif;?>
        <?php if ($_show_date) : ?>
            <div class="published-time entry-meta-published-time">
                <span class="date-icon icon-text"><i class="ic-clock"></i></span>
                <a class="published-link" href="<?= esc_url($_permalink); ?>" rel="bookmark"><?= $_time; ?></a>
            </div>
        <?php endif;?>
        <?php if ($_show_author && $_post) : ?>
            <div class="author entry-meta-author">
                <span class="author-icon icon-text"><i class="ic-user"></i></span>
                <a class="author-link"
                   title="<?= esc_attr(
                       sprintf( __( 'See all post by: %s', 'law-lib' ),
                           get_the_author() )
                   ); ?>"
                   href="<?= esc_url(get_author_posts_url( $_user->ID)); ?>"
                   rel="author">
                    <?= esc_html( $_user->display_name ); ?>
                </a>
            </div>
        <?php endif;?>
    </div>
    <?php /* Content Entry Meta */ ?>
<?php endif;?>


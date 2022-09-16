<?php
if (!defined('ABSPATH')) :
    return;
endif;

$_post_id = $args['post_id']??get_the_ID();
$_is_singular = is_single($_post_id);
if (!$_is_singular) {
    return;
}
$_the_post = get_post($_post_id);
if (!isset($_the_post->post_author)) {
    return;
}
$_display_name = get_the_author_meta( 'display_name', $post->post_author );
if (empty($_display_name) || !is_string($_display_name) || trim($_display_name) === '') {
    $_display_name = get_the_author_meta( 'nickname', $post->post_author );
}
$_user_description = get_the_author_meta( 'user_description', $post->post_author );
$_user_description = is_string($_user_description) ? strip_tags($_user_description) : '';
if (strlen($_user_description) >= 120) {
    $_user_description = substr($_user_description, 0, 120) . '...';
}
$_user_website = get_the_author_meta('url', $post->post_author);
$_user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));
$_avatar = get_avatar( get_the_author_meta('user_email', $post->post_author) , 90 );
$_title_post_link = esc_html(
    sprintf(
        __('Show all post by: %s','law-lib'),
        $_display_name
    )
);
?>
    <div id="author-bio" class="author-bio">
        <div class="author-bio-wrapper">
            <div class="author-avatar">
                <?= $_avatar;?>
            </div>
            <div class="author-name">
                <a href="<?= esc_attr($_user_posts);?>" title="<?= $_title_post_link;?>">
                    <?= esc_html($_display_name);?>
                </a>
            </div>
            <?php if ($_user_website) : ?>
            <div class="author-link">
                <a href="<?= esc_attr($_user_website);?>" rel="nofollow noopener" target="_blank">
                   <?php esc_html_e('Website', 'law-lib');?>
                </a>
            </div>
            <div class="author-detail"><?= $_user_description;?></div>
            <?php endif;?>
        </div>
    </div>
<?php

<?php
if (!defined('ABSPATH')) :
    return;
endif;

?>
    <?php do_action('before_site_footer');?>
    <footer id="footer" class="section footer-section">
        <div class="footer-container container">
            <?php do_action('before_footer_sidebar');?>
            <?php dynamic_sidebar('footer-sidebar');?>
            <?php do_action('after_footer_sidebar');?>
            <?php
                $_copyright = law_lib_component_option(
                    'global',
                    'copyright_text',
                    '<div class="text-center text-small">{copy} {year} <a href="{url}" class="home-url" rel="home">{name}</a>. All Right Reserved.</div>'
                );
                if (is_string($_copyright) && trim($_copyright)) :
                    $_copyright = law_lib_component_shorthand_replace($_copyright);
            ?>
            <div class="copyright">
                <?= $_copyright;?>
            </div>
            <?php endif;?>
        </div>
    </footer>
    <?php do_action('after_site_footer');?>

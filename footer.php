<?php
if (!defined('ABSPATH')) :
    return;
endif;
?>
                </div>
                <!-- #content -->
                <?php do_action('after_content');?>
            </div>
            <!-- #primary -->
            <?php do_action('after_primary');?>

        </main>
        <!-- #main -->
        <?php do_action('after_main');?>

        <?php get_template_part('templates/site/site-footer');?>
        <?php get_template_part('templates/site/site-mobile-search');?>
        <?php get_template_part('templates/site/site-mobile-menu');?>
    </div>
    <!-- #page -->
<?php wp_footer(); ?>
<?php get_template_part('templates/site/site-sidebar');?>
</body>
</html>
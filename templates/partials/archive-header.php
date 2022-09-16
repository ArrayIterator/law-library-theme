<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!is_archive() && !is_search() || is_404()) {
    return;
}

$_is_search   = is_search();
$_description = law_lib_component_option('global', 'show_archive_description', 'yes') !== 'no'
    ? get_the_archive_description()
    : false;
?>
<?php if ( $_is_search || have_posts() ) : ?>
    <div class="archive-header">
        <?php if ( $_is_search ) : ?>
            <h1 class="page-title search-title">
                <?php printf(
                    sprintf(
                        __( 'Search Results For: %s', 'law-lib' ),
                        sprintf(
                            '<span class="result-query">%s</span>',
                            get_search_query()
                        )
                    )
                ); ?>
            </h1>
        <?php else : ?>
            <?php law_lib_component_option('global', 'show_archive_title', 'yes') !== 'no' ? the_archive_title( '<h1 class="page-title">', '</h1>' ) : false; ?>
            <?php if ( $_description ) : ?>
                <div class="archive-description"><?php echo wp_kses_post( wpautop( $_description ) ); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

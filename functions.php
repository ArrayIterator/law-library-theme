<?php
/**
 * @author arrayiterator
 */
if (!defined('ABSPATH')) :
    return;
endif;

// CLASS
require_once __DIR__ .'/inc/classes/Law_Lib_Customizer_Multi_Select_Control.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Mega_Menu_Walker.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Mobile_Menu_Walker.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Post_Rest.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Popular_Views.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Rest_Response.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Sitemap.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Sitemap_Renderer.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Sidebar_Amp_Walker.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Image.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Meta_Data.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Resolver.php';
require_once __DIR__ .'/inc/classes/Law_Lib_Meta_Query.php';
// AMP
require_once __DIR__ .'/inc/classes/Amp/Law_Lib_Amp_Component.php';
require_once __DIR__ .'/inc/classes/Amp/Law_Lib_Amp_Carousel.php';
require_once __DIR__ .'/inc/classes/Amp/Law_Lib_Amp_Carousel_List.php';
require_once __DIR__ .'/inc/classes/Widgets/Law_Lib_Widget_SpecialHTML.php';
require_once __DIR__ .'/inc/component.php';
require_once __DIR__ .'/inc/metadata.php';
require_once __DIR__ .'/inc/logic.php';
require_once __DIR__ .'/inc/notice.php';
require_once __DIR__ .'/inc/return.php';


// HOOKS
require_once __DIR__ .'/inc/hooks/addition.php';
require_once __DIR__ .'/inc/hooks/amp.php';
require_once __DIR__ .'/inc/hooks/attributes.php';
require_once __DIR__ .'/inc/hooks/check.php';
require_once __DIR__ .'/inc/hooks/customizer.php';
require_once __DIR__ .'/inc/hooks/components.php';
require_once __DIR__ .'/inc/hooks/formatting.php';
require_once __DIR__ .'/inc/hooks/menu.php';
require_once __DIR__ .'/inc/hooks/menu-icon.php';
require_once __DIR__ .'/inc/hooks/scripts.php';
require_once __DIR__ .'/inc/hooks/setup.php';
require_once __DIR__ .'/inc/hooks/sitemap.php';

require_once __DIR__ .'/blocks/blocks.php';

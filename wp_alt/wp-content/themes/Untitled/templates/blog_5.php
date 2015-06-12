<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<!DOCTYPE html>
<html dir="ltr" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset') ?>" />
    
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <script>
    var themeHasJQuery = !!window.jQuery;
</script>
<script src="<?php echo get_bloginfo('template_url', 'display') . '/jquery.js?ver=' . wp_get_theme()->get('Version'); ?>"></script>
<script>
    window._$ = jQuery.noConflict(themeHasJQuery);
</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link class="" href='http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,regular,italic,600,600italic,700,700italic,800,800italic&subset=latin' rel='stylesheet' type='text/css'>
<script src="<?php echo get_bloginfo('template_url', 'display'); ?>/CloudZoom.js?ver=<?php echo wp_get_theme()->get('Version'); ?>" type="text/javascript"></script>
    
    <?php wp_head(); ?>
    
    <!--[if lte IE 9]>
    <link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_url', 'display') . '/style.ie.css?ver=' . wp_get_theme()->get('Version'); ?>" />
    <![endif]-->
</head>
<body <?php body_class(' bootstrap bd-body-5 bd-pagebackground'); ?>>
<header class=" bd-headerarea-1">
        <div class="container  bd-containereffect-1"><div class=" bd-layoutbox-5 clearfix">
    <div class="bd-container-inner">
        <?php theme_logo_1(); ?>
	
		<?php
    if (theme_woocommerce_enabled()) {
        echo theme_get_link_6();
    }
?>
	
		<form id="search-3" class=" bd-search-3 form-inline" method="<?php echo isset($_GET['preview']) ? 'post' : 'get'; ?>" name="searchform" action="<?php echo esc_url( home_url() ); ?>/">
    <div class="bd-container-inner">
        <div class="bd-search-wrapper">
            
                <input name="s" type="text" class=" bd-bootstrapinput-5 form-control" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Jetzt suchen">
                <a href="#" class=" bd-icon-28" link-disable="true"></a>
        </div>
    </div>
    <script>
        (function (jQuery, $) {
            jQuery('.bd-search-3 .bd-icon-28').on('click', function (e) {
                e.preventDefault();
                jQuery('#search-3').submit();
            });
        })(window._$, window._$);
    </script>
</form>
	
		<?php
    theme_hmenu_1();
?>
    </div>
</div>
</div>
</header>
	
		<div class="bd-sheetstyles bd-contentlayout-5 ">
    <div class="bd-container-inner">
        <div class="bd-flex-vertical">
            
 <?php theme_sidebar_area_6(); ?>
            <div class="bd-flex-horizontal bd-flex-wide">
                
 <?php theme_sidebar_area_5(); ?>
                <div class="bd-flex-vertical bd-flex-wide">
                    
                    <div class=" bd-layoutitemsbox-1 bd-flex-wide">
    <div class=" bd-content-1">
    <div class="bd-container-inner">
    
    <?php theme_print_content(); ?>
    </div>
</div>
</div>
                    
                </div>
                
            </div>
            
        </div>
    </div>
</div>
	
		<footer class=" bd-footerarea-1">
        <div class=" bd-layoutcontainer-28">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class=" 
 col-md-6
 col-sm-12
 col-xs-24">
    <div class="bd-layoutcolumn-62"><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer1", 'footer_2_3');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer1', ' bd-footerwidgetarea-3 clearfix');
?></div></div>
</div>
	
		<div class=" 
 col-md-6
 col-sm-12
 col-xs-24">
    <div class="bd-layoutcolumn-63"><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer2", 'footer_3_4');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer2', ' bd-footerwidgetarea-4 clearfix');
?></div></div>
</div>
	
		<div class=" 
 col-md-6
 col-sm-12
 col-xs-24">
    <div class="bd-layoutcolumn-64"><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer3", 'footer_4_6');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer3', ' bd-footerwidgetarea-6 clearfix');
?></div></div>
</div>
	
		<div class=" 
 col-md-6
 col-sm-12
 col-xs-24">
    <div class="bd-layoutcolumn-65"><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer4", 'footer_6_8');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer4', ' bd-footerwidgetarea-8 clearfix');
?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
</footer>
	
		<div data-animation-time="250" class=" bd-smoothscroll-3"><a href="#" class=" bd-backtotop-1">
    <span class=" bd-icon-67"></span>
</a></div>
<div id="wp-footer">
    <?php wp_footer(); ?>
    <!-- <?php printf(__('%d queries. %s seconds.', THEME_NS), get_num_queries(), timer_stop(0, 3)); ?> -->
</div>
</body>
</html>
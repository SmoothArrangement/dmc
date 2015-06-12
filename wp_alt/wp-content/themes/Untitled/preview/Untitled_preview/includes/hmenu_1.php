<?php

register_nav_menus(array('primary-menu-1' => __('Primary Navigation 1', THEME_NS)));

function theme_hmenu_1() {
    $ID = 1;
?>
    
    <nav class="data-control-id-754 bd-hmenu-1" data-responsive-menu="true" data-responsive-levels="">
        
            <div class="data-control-id-1047262 bd-menuitem-4 collapse-button">
    <a  data-toggle="collapse"
        data-target=".bd-hmenu-1 .collapse-button + .navbar-collapse"
        href="#" onclick="return false;">
            <span>Navigation</span>
    </a>
</div>
            <div class="navbar-collapse collapse">
        
        <div class="data-control-id-171385 bd-horizontalmenu-2 clearfix">
            <div class="bd-container-inner">
            <?php
                echo theme_get_menu(array(
                        'source' => theme_get_option('theme_menu_source'),
                        'depth' => theme_get_option('theme_menu_depth'),
                        'menu' => 'primary-menu-'.$ID,
                        'responsive_levels' => '',
                        'levels' => '',
                        'menu_function' => 'theme_menu_'.$ID.'_3',
                        'menu_item_start_function' => 'theme_menu_item_start_'.$ID.'_3',
                        'menu_item_end_function' => 'theme_menu_item_end_'.$ID.'_3',
                        'submenu_start_function' => 'theme_submenu_start_'.$ID.'_4',
                        'submenu_end_function' => 'theme_submenu_end_'.$ID.'_4',
                        'submenu_item_start_function' => 'theme_submenu_item_start_'.$ID.'_6',
                        'submenu_item_end_function' => 'theme_submenu_item_end_'.$ID.'_6'
                    )
                );
            ?>
            </div>
        </div>
        
        
            </div>
    </nav>
    
<?php
}

function theme_menu_1_3($content = '') {
    ob_start();
    ?><ul class="data-control-id-171386 bd-menu-3 nav nav-pills navbar-left">
    <?php echo $content; ?>
</ul><?php
    return ob_get_clean();
}

function theme_menu_item_start_1_3($class = '', $title = '', $attrs = '', $link_class='') {
    ob_start();
    ?><li class="data-control-id-171387 bd-menuitem-3 <?php echo $class; ?>">
    <a class="<?php echo $link_class; ?>" <?php echo $attrs; ?>>
        <span>
            <?php echo $title; ?>
        </span>
    </a><?php
    return ob_get_clean();
}

function theme_menu_item_end_1_3() {
    ob_start();
?>
    </li>
    
<?php
    return ob_get_clean();
}

function theme_submenu_start_1_4($class = '') {
    ob_start();
    ?><div class="bd-menu-4-popup">
    <ul class="data-control-id-171405 bd-menu-4 <?php echo $class; ?>"><?php
    return ob_get_clean();
}

function theme_submenu_end_1_4() {
    ob_start();
?>
        </ul>
    </div>
    
<?php
    return ob_get_clean();
}

function theme_submenu_item_start_1_6($class = '', $title = '', $attrs = '', $link_class = '') {
    ob_start();
    ?><li class="data-control-id-171406 bd-menuitem-6 <?php echo $class; ?>">
    <a class="<?php echo $link_class; ?>" <?php echo $attrs; ?>>
        <span>
            <?php echo $title; ?>
        </span>
    </a><?php
    return ob_get_clean();
}

function theme_submenu_item_end_1_6() {
    ob_start();
?>
    </li>
    
<?php
    return ob_get_clean();
}
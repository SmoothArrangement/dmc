<?php
    function theme_sidebar_area_6() {
        $theme_hide_sidebar_area = true;
        ob_start();
?>
        <?php theme_breadcrumbs_1(); ?>
        <?php $area_content = trim(ob_get_clean()); ?>

        <?php if (theme_preview_params_exists()): ?>
            <?php $hide = 
 $theme_hide_sidebar_area ||
                !strlen(trim(preg_replace('/<!-- empty::begin -->[\s\S]*?<!-- empty::end -->/', '', $area_content))); /* no other controls */ ?>

            <div class="bd-sidebararea-6-column  bd-flex-vertical bd-flex-fixed<?php if ($hide) echo ' hidden bd-hidden-sidebar'; ?>">
                <div class="bd-sidebararea-6 bd-flex-wide">
                    <?php echo $area_content ?>
                </div>
            </div>
        <?php else: ?>
            <?php if ($area_content
 && !$theme_hide_sidebar_area): ?>
                <div class="bd-sidebararea-6-column  bd-flex-vertical bd-flex-fixed">
                    <div class="bd-sidebararea-6 bd-flex-wide">
                        <?php echo $area_content ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
<?php
    }
?>
<?php
function theme_block_footer_6_8($title = '', $content = '', $class = '', $id = ''){
?>
    <div class="data-control-id-2760 bd-block-6 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class="data-control-id-2727 bd-container-48 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class="data-control-id-2759 bd-container-49 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
}
?>
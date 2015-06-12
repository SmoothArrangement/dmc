<?php
function theme_block_footer_3_4($title = '', $content = '', $class = '', $id = ''){
?>
    <div class="data-control-id-2626 bd-block-3 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class="data-control-id-2593 bd-container-40 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class="data-control-id-2625 bd-container-42 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
}
?>
<?php
function theme_block_default_1_2($title = '', $content = '', $class = '', $id = ''){
?>
    <div class="data-control-id-74926 bd-block <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class="data-control-id-74927 bd-container-1 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class="data-control-id-74928 bd-container-2 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
}
?>
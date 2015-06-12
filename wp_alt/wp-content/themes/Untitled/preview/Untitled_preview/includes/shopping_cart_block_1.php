<?php
function theme_shopping_cart_block_1($class = '', $id = '', $title = '', $content = ''){
?>
    
    <div class="data-control-id-2963 bd-block <?php echo $class; ?>" id="<?php echo $id; ?>">
        <div class="bd-container-inner">
            <?php if (!theme_is_empty_html($title)){ ?>
<div class="data-control-id-2930 bd-container-1 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
    <h4><?php echo $title; ?></h4>
</div>
<?php }?>
            <div class="data-control-id-2962 bd-container-2 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table">
    <?php echo $content; ?>
</div>
        </div>
    </div>
    
<?php
}
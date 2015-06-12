<?php
function theme_shopping_cart() {
?>
    <div class="data-control-id-2964 bd-shoppingcart">
<?php
            global $post;
            if (have_posts()) {
                while ( have_posts() ) {
                    the_post();
                    $title = '<a href="' . get_permalink($post->ID) . '" rel="bookmark" title="' . strip_tags(get_the_title()) . '">' . get_the_title() . '</a>';
                    if (!theme_is_empty_html($title)) {
?>
                        <div class="data-control-id-2820 bd-carttitle-1">
    <h2><?php echo $title; ?></h2>
</div>
<?php
                    }
                    $content = theme_get_content();
                    echo $content;
                }
            } else {
                theme_404_content();
            }
?>

        <?php if (theme_woocommerce_enabled()): ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $title = __('Calculate Shipping', 'woocommerce');
                        ob_start();
                    ?>
                    <?php woocommerce_shipping_calculator(); ?>
                    <?php
                        $content = ob_get_clean();

                        $class = 'data-control-id-2963 bd-block';
                        $id = 'shipping-calculator';
                        if (!theme_is_empty_html($content)) {
                            echo theme_shopping_cart_block_1_1($title, $content, $class, $id);
                        }
                    ?>
                </div>
                <div class="col-md-12">
                    <?php
                        $title = __('Cart Totals', 'woocommerce');

                        ob_start();
                    ?>
                        <?php woocommerce_cart_totals(); ?>
                    <?php
                        $content = ob_get_clean();

                        $class = 'data-control-id-2963 bd-block';
                        $id = 'cart-totals';
                        if (!theme_is_empty_html($content)) {
                            echo theme_shopping_cart_block_1_1($title, $content, $class, $id);
                        }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}

function theme_shopping_cart_block_1_1($title = '', $content = '', $class = '', $id = ''){
?>
    <?php theme_shopping_cart_block_1($class, $id, $title, $content); ?>
<?php
}
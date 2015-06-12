<?php

function theme_cart_link_fragments_filter_6($fragment) {
    $fragment['.bd-cartlink-6'] = theme_get_link_6();
    return $fragment;
}
add_filter('add_to_cart_fragments', 'theme_cart_link_fragments_filter_6');
add_filter('add_to_cart_fragments', 'theme_add_to_cart_fragments', 20);

function theme_get_link_6() {
    ob_start();
    global $woocommerce;
?>

<div class=" bd-cartlink-6">
    
    <div class=" bd-horizontalmenu-9 clearfix">
        <div class="bd-container-inner">
            
            <ul class=" bd-menu-18 nav nav-pills navbar-left">
                <li class=" bd-menuitem-22">
                    <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">
                        
                        
                            <?php global $woocommerce; ?>
<div class=" bd-pricetext-22">
    
        <span class=" bd-container-59 bd-tagstyles">
            <?php echo $woocommerce->cart->get_cart_subtotal(); ?>
        </span>
</div>
                    </a>
                    <?php if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) : ?>
                        <div class="bd-menu-19-popup">
                            
                            <ul class=" bd-menu-19">
                                <li class=" bd-menuitem-25">
                                    <div class=" bd-cartcontainer-7">
    <?php
    global $woocommerce, $product;
    if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ){
        ?><div class=" bd-grid-9">
           <div class="container-fluid">
            <div class="separated-grid row"><?php
            $current_product = $product; // save current product
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ){
                $product = $cart_item['data']; // set cart product
                // Only display if allowed
                if ( ! apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $product->exists() || $cart_item['quantity'] == 0 )
                    continue;
                $product_price = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || $woocommerce->customer->is_vat_exempt() ? $product->get_price_excluding_tax() : $product->get_price();
                $product_price = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
                $product_view = array();
                $product_view['link']  = get_permalink( $cart_item['product_id'] );
                $product_view['title'] = apply_filters('woocommerce_widget_cart_product_title', $product->get_title(), $product );
                $product_view['price'] = $product_price;
                $product_view['image']  = $product->get_image();
                ?>
                <div class="separated-item-31 col-md-24 list">
    <div class=" bd-griditem-31"><div class=" 
 col-md-6">
    <div class="bd-layoutcolumn-94"><div class="bd-vertical-align-wrapper"><a class=" bd-productimage-10" href="<?php echo $product_view['link']; ?>"><?php theme_product_image($product_view, ' bd-imagestyles'); ?></a></div></div>
</div>
	
		<div class=" 
 col-md-14">
    <div class="bd-layoutcolumn-96"><div class="bd-vertical-align-wrapper"><?php if ( isset($product_view['link']) && isset($product_view['title']) ){ ?><div class=" bd-producttitle-16"><a href="<?php echo $product_view['link']; ?>"><?php echo $product_view['title']; ?></a></div><?php } ?>
	
		<div class=" bd-cartprice-5">
    <?php echo $cart_item['quantity']; ?> x <div class=" bd-pricetext-23">
    <?php
        if (isset($product_view['price'])){
            echo $product_view['price'];
        }
    ?>
</div>
</div></div></div>
</div>
	
		<div class=" 
 col-md-4">
    <div class="bd-layoutcolumn-97"><div class="bd-vertical-align-wrapper">
	
		<?php
    global $woocommerce;
    $href = $woocommerce->cart->get_remove_url($cart_item_key) . '&_wp_http_referer=' . urlencode($woocommerce->cart->get_cart_url());
?>
<a class=" bd-itemremovelink-5" href="<?php echo $href; ?>">
    <span class=" bd-icon-38"></span>
</a></div></div>
</div></div>
</div>
                <?php
            }
            $product = $current_product; // restore current product
        ?></div></div></div><?php
    }
?>
	
		<?php global $woocommerce; ?>
<div class=" bd-pricetext-24">
    <span class=" bd-label-26">Total:</span>
        <span class=" bd-container-61 bd-tagstyles">
            <?php echo $woocommerce->cart->get_cart_subtotal(); ?>
        </span>
</div>
	
		<div class=" bd-layoutcontainer-13">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class=" 
 col-md-7">
    <div class="bd-layoutcolumn-102"><div class="bd-vertical-align-wrapper"><?php global $woocommerce; ?>
<a href="<?php echo theme_woocommerce_enabled() ? $woocommerce->cart->get_cart_url() : ''; ?>" class=" bd-button">View</a></div></div>
</div>
	
		<div class=" 
 col-md-17">
    <div class="bd-layoutcolumn-104"><div class="bd-vertical-align-wrapper"><?php global $woocommerce; ?>
<a href="<?php echo $woocommerce->cart->get_checkout_url(); ?>" class=" bd-button">Proceed to Checkout</a></div></div>
</div>
            </div>
        </div>
    </div>
</div>
</div>
                                </li>
                            </ul>
                            
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
            
        </div>
    </div>
    
</div>

<?php
    return ob_get_clean();
}
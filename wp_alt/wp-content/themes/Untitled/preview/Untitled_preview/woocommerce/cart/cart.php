<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php function_exists('wc_print_notices') ? wc_print_notices() : $woocommerce->show_messages(); ?>

<?php global $woocommerce; ?>
<form action="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" method="post" class="data-control-id-2848 bd-shoppingcarttable-1">
    <?php do_action( 'woocommerce_before_cart_table' ); ?>
    <div class="table-responsive">
    <table class="data-control-id-2838 bd-table">
        <colgroup>
            <col width="1">
            <col>
            <col width="1">
            <col width="1">
            <col width="1">
            <col width="1">
        </colgroup>
        <thead>
            <tr>
                <th class="product-thumbnail">&nbsp;</th>
                <th class="product-name"><?php _e('Product', 'woocommerce'); ?></th>
                <th class="product-price"><?php _e('Price', 'woocommerce'); ?></th>
                <th class="product-quantity"><?php _e('Quantity', 'woocommerce'); ?></th>
                <th class="product-subtotal"><?php _e('Total', 'woocommerce'); ?></th>
                <th class="product-remove">&nbsp;</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
                    <div class="data-control-id-44550 bd-container-4 bd-tagstyles bd-custom-button bd-custom-bootstrapinput">
                        <input class="data-control-id-44543 bd-button-22" type="submit" name="update_cart" value="<?php _e('Update Cart', 'woocommerce'); ?>" />
                        <input class="data-control-id-44543 bd-button-22" type="submit" name="proceed" value="<?php _e('Proceed to Checkout &rarr;', 'woocommerce'); ?>" />
                        <?php
                            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout'); // remove default button
                            do_action('woocommerce_proceed_to_checkout');
                            echo theme_get_wc_nonce_field('cart');

                            if ( get_option( 'woocommerce_enable_coupons' ) == 'yes' && get_option( 'woocommerce_enable_coupon_form_on_cart' ) == 'yes') { ?>
                                <div class="form-inline form-responsive-dependent-float">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="coupon_code" id="coupon_code" value="" placeholder="<?php _e('Coupon', 'woocommerce'); ?>" />
                                    </div>
                                    <input class="data-control-id-44543 bd-button-22" type="submit" name="apply_coupon" value="<?php _e('Apply Coupon', 'woocommerce'); ?>"/>
                                </div>
                                <?php  do_action('woocommerce_cart_coupon');
                            }
                        ?>
                    </div>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php do_action( 'woocommerce_before_cart_contents' ); ?>

            <?php
            if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
                $i = 1;
                foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
                    $_product = $values['data'];
                    if ( $_product->exists() && $values['quantity'] > 0 ) {
                        ?>
                        <tr <?php if ($i % 2 === 0): ?>class="alt"<?php endif ?>>

                            <!-- The thumbnail -->
                            <td class="product-thumbnail">
                                <?php $thumbnail = apply_filters( 'woocommerce_in_cart_product_thumbnail', $_product->get_image('shop_thumbnail', array('class' => 'data-control-id-2825 bd-imagestyles-8')), $values, $cart_item_key ); ?>
                                <a href="<?php echo esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $values['product_id'] ) ) ); ?>"><?php echo $thumbnail; ?></a>
                            </td>


                            <!-- Product Name -->
                            <td class="product-name">
                                <div class="data-control-id-2846 bd-producttext-12">
    <?php
        if ( ! $_product->is_visible() || ( $_product instanceof WC_Product_Variation && ! $_product->parent_is_visible() ) )
            echo apply_filters( 'woocommerce_in_cart_product_title', $_product->get_title(), $values, $cart_item_key );
        else
            printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $values['product_id'] ) ) ), apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $values, $cart_item_key ) );

        // Meta data
        echo $woocommerce->cart->get_item_data( $values );

        // Backorder notification
        if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $values['quantity'] ) )
            echo '<p class="backorder_notification">' . __('Available on backorder', 'woocommerce') . '</p>';
    ?>
</div>
                            </td>

                            <!-- Product price -->
                            <td class="product-price">
                                <?php
                                $product_price = get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();

                                echo apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $values, $cart_item_key );
                                ?>
                            </td>

                            <!-- Quantity inputs -->
                            <td class="product-quantity">
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    $product_quantity =  sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                } else {
                                    $data_min = apply_filters( 'woocommerce_cart_item_data_min', '', $_product );
                                    $data_max = ( $_product->backorders_allowed() ) ? '' : $_product->get_stock_quantity();
                                    $data_max = apply_filters( 'woocommerce_cart_item_data_max', $data_max, $_product );

                                    $product_quantity = sprintf( '<div class="quantity"><input type="text" name="cart[%s][qty]" data-min="%s" data-max="%s" value="%s" size="4" title="Qty" class="qty data-control-id-2847 bd-bootstrapinput-6 form-control input-sm" maxlength="12" /></div>', $cart_item_key, $data_min, $data_max, esc_attr( $values['quantity'] ));
                                }

                                echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
                                ?>
                            </td>

                            <!-- Product subtotal -->
                            <td class="product-subtotal">
                                <?php
                                echo apply_filters( 'woocommerce_cart_item_subtotal', $woocommerce->cart->get_product_subtotal( $_product, $values['quantity'] ), $values, $cart_item_key );
                                ?>
                            </td>

                            <!-- Remove from cart link -->
                            <td class="product-remove">
                                <?php
                                    $href = esc_url($woocommerce->cart->get_remove_url( $cart_item_key ) . '&_wp_http_referer=' . urlencode($woocommerce->cart->get_cart_url()));
                                    $title = __('Remove this item', 'woocommerce');
                                    echo apply_filters( 'woocommerce_cart_item_remove_link', '<a class="removelink" href="'.$href.'" title="'.$title.'"><span class="data-control-id-2845 bd-icon-69"></span></a>', $cart_item_key );
                                ?>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    }
                }
            }

            do_action( 'woocommerce_cart_contents' );
            ?>
        </tbody>
    </table>
    </div>
    <?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
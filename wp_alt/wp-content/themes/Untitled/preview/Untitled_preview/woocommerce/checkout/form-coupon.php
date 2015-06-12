<?php
/**
 * Checkout coupon form
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( get_option( 'woocommerce_enable_coupons' ) == 'no' || get_option( 'woocommerce_enable_coupon_form_on_checkout' ) == 'no' ) return;

$info_message = apply_filters('woocommerce_checkout_coupon_message', __('Have a coupon?', 'woocommerce'));
?>

<?php ob_start(); ?>
    <?php echo $info_message; ?> <a href="#" class="showcoupon"><?php _e('Click here to enter your code', 'woocommerce'); ?></a>
<?php theme_information_message(ob_get_clean()); ?>

<form class="checkout_coupon" method="post">

	<p class="form-row form-row-first">
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php _e('Coupon code', 'woocommerce'); ?>" id="coupon_code" value="" />
	</p>

	<p class="form-row form-row-last">
		<input type="submit" class="button" name="apply_coupon" value="<?php _e('Apply Coupon', 'woocommerce'); ?>" />
	</p>

	<div class="clear"></div>
</form>
<?php
/**
 * Checkout login form
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() ) return;
if ( get_option('woocommerce_enable_signup_and_login_from_checkout') == "no" ) return;

$info_message = apply_filters('woocommerce_checkout_login_message', __('Already registered?', 'woocommerce'));
?>

<?php ob_start(); ?>
    <?php echo $info_message; ?> <a href="#" class="showlogin"><?php _e('Click here to login', 'woocommerce'); ?></a>
<?php theme_information_message(ob_get_clean()); ?>

<?php woocommerce_login_form( array( 'message' => __('If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'woocommerce'), 'redirect' => get_permalink(woocommerce_get_page_id('checkout')) ) ); ?>
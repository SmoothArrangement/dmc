<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$woocommerce_loop['loop']++;

global $current_category;
$current_category = $category;
?>

<div class=" bd-grid-43">
    <div class="container-fluid">
        <div class="separated-grid row">
            
                <span class=" bd-icon-18"></span>
            <div class="separated-item-11 col-md-24 grid">
    <div class=" bd-griditem-11"></div>
</div>
        </div>
    </div>
</div>

<?php unset($current_category); ?>
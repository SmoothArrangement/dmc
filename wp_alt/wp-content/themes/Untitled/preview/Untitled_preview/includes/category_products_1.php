<?php
function theme_products() {
?>
    
    <div class="data-control-id-2494 bd-products">
        
            <div class="data-control-id-54692 bd-container-54 bd-tagstyles">
    <h2>
        <?php
            if ( is_search() ) {
                printf( __( 'Search Results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );
                if ( get_query_var( 'paged' ) )
                    printf( __( '&nbsp;&ndash; Page %s', 'woocommerce' ), get_query_var( 'paged' ) );
            } elseif ( is_tax() ) {
                echo single_term_title( "", false );
            } else {
                $shop_page = get_post( woocommerce_get_page_id( 'shop' ) );
                echo apply_filters( 'the_title', ( $shop_page_title = get_option( 'woocommerce_shop_page_title' ) ) ? $shop_page_title : $shop_page->post_title );
            }
        ?>
    </h2>
</div>

        

        <?php
            if ( have_posts() ) :
                do_action('woocommerce_before_shop_loop');
        ?>
                <div class="data-control-id-54742 bd-productsgridbar-35">
    <div class="bd-container-inner">
        <div class="data-control-id-2446 bd-layoutcontainer-27">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class="data-control-id-2442 
 col-md-12
 col-sm-12
 col-xs-8">
    <div class="bd-layoutcolumn-60"><div class="bd-vertical-align-wrapper"><div class="data-control-id-2448 bd-typeselector-1">
    
</div></div></div>
</div>
	
		<div class="data-control-id-2444 
 col-md-12
 col-sm-12
 col-xs-16">
    <div class="bd-layoutcolumn-61"><div class="bd-vertical-align-wrapper"><div class="data-control-id-2450 bd-productssorter-1">
    
    <?php woocommerce_catalog_ordering(); ?>
</div></div></div>
</div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
                <div class="data-control-id-771834 bd-grid-45">
                  <div class="container-fluid">
                    <div class="separated-grid row">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php
                            global $product, $woocommerce_loop, $post;

                            // Store loop count we're currently on
                            if ( empty( $woocommerce_loop['loop'] ) )
                                $woocommerce_loop['loop'] = 0;

                            // Store column count for displaying the grid
                            if ( empty( $woocommerce_loop['columns'] ) )
                                $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

                            // Ensure visibilty
                            if ( !$product || !$product->is_visible() )
                                return;

                            // Increase loop count
                            $woocommerce_loop['loop']++;

                            $product_view = array();
                            $product_view['link']  = apply_filters('the_permalink', get_permalink());
                            $product_view['title'] = the_title('', '', false);
                            $product_view['price'] = theme_get_price_data($product);
                            $product_view['desc']  = $post->post_excerpt;
                            $product_view['image'] = woocommerce_get_product_thumbnail('shop_catalog', '', '');

                            $cart_item = isset($cart_item) ? $cart_item : array();
                            $cart_item_key = isset($cart_item_key) ? $cart_item_key : '';
                            $product_view['dafaultLayoutName'] = "grid";
                            $product_view['layouts_count'] = 2;
                            $product_view['activeLayoutName'] = isset($_COOKIE['layoutType']) ? $_COOKIE['layoutType'] : $product_view['dafaultLayoutName'];
                            ?>
                            
                            <div class="separated-item-4 col-md-6 col-sm-12 grid"<?php if ('grid' !== $product_view['activeLayoutName']) { echo 'style="display:none;"';} ?>>
                                <div class="data-control-id-2471 bd-griditem-4">
                                    <a class="data-control-id-2270 bd-productimage-4" href="<?php echo $product_view['link']; ?>"><?php theme_product_image($product_view, 'data-control-id-2269 bd-imagestyles-16'); ?></a>
	
		<?php if ( isset($product_view['link']) && isset($product_view['title']) ){ ?><div class="data-control-id-2271 bd-producttitle-8"><a href="<?php echo $product_view['link']; ?>"><?php echo $product_view['title']; ?></a></div><?php } ?>
	
		<div class="data-control-id-2343 bd-productprice-3">
    <?php
        if (isset($product_view['price'])) {
    ?>
    <span class="price"><?php
        echo theme_price_html(
            $product_view['price'], false, true,
            '<div class="data-control-id-2342 bd-pricetext-11">', '</div>',
            
 false, 'data-control-id-2341 bd-container-30 bd-tagstyles',
            '<div class="data-control-id-2308 bd-pricetext-10">', '</div>',
            
 false, 'data-control-id-2307 bd-container-29 bd-tagstyles');
?></span>
<?php
        }
?>
</div>
	
		<div class="data-control-id-2273 bd-productdesc-9">
    <?php
        if ( isset($product_view['desc']) && $product_view['desc'] ){
            $desc = apply_filters( 'woocommerce_short_description', $product_view['desc'] );
            $desc = wp_strip_all_tags( $desc, true );
            echo substr($desc, 0, 65) . preg_replace('/[\s!\?\.;][\s\S]*/', '...', substr($desc, 65));
        }
    ?>
</div>
	
		<?php theme_product_buy('data-control-id-2348 bd-productbuy-2 bd-button'); ?>
	
		<?php if (time() - get_option('theme_products_newness_period') * 60 * 60 * 24 < strtotime(get_the_time('Y-m-d'))) { ?>
<div class="data-control-id-2350 bd-productnewicon-3 bd-productnew-2">
    <span><?php _e('New!', 'woocommerce'); ?></span>
</div>
<?php } ?>
	
		<?php if ($product->is_on_sale()): ?>
<div class="data-control-id-2352 bd-productsaleicon bd-productsale-2">
    <span><?php _e('Sale!', 'woocommerce'); ?></span>
</div>
<?php endif; ?>
	
		<?php
    if (!$product->is_in_stock()) :
?>
        <div class="data-control-id-2354 bd-productoutofstockicon bd-productoutofstock-2">
            <span>
                <?php _e('Out of stock', 'woocommerce'); ?>
            </span>
        </div>
<?php
    endif;
?>
                                </div>
                            </div>
                            <div class="separated-item-5 col-md-24 list"<?php if ('list' !== $product_view['activeLayoutName']) { echo 'style="display:none;"';} ?>>
                                <div class="data-control-id-2486 bd-griditem-5">
                                    <div class="data-control-id-54401 bd-layoutcontainer-26">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class="data-control-id-2473 
 col-md-6
 col-sm-12">
    <div class="bd-layoutcolumn-57"><div class="bd-vertical-align-wrapper"><a class="data-control-id-2356 bd-productimage-5" href="<?php echo $product_view['link']; ?>"><?php theme_product_image($product_view, 'data-control-id-2355 bd-imagestyles'); ?></a>
	
		<?php if (time() - get_option('theme_products_newness_period') * 60 * 60 * 24 < strtotime(get_the_time('Y-m-d'))) { ?>
<div class="data-control-id-2358 bd-productnewicon-2 bd-productnew-3">
    <span><?php _e('New!', 'woocommerce'); ?></span>
</div>
<?php } ?>
	
		<?php if ($product->is_on_sale()): ?>
<div class="data-control-id-2360 bd-productsaleicon bd-productsale-3">
    <span><?php _e('Sale!', 'woocommerce'); ?></span>
</div>
<?php endif; ?>
	
		<?php
    if (!$product->is_in_stock()) :
?>
        <div class="data-control-id-2362 bd-productoutofstockicon bd-productoutofstock-3">
            <span>
                <?php _e('Out of stock', 'woocommerce'); ?>
            </span>
        </div>
<?php
    endif;
?></div></div>
</div>
	
		<div class="data-control-id-2475 
 col-md-13">
    <div class="bd-layoutcolumn-58"><div class="bd-vertical-align-wrapper"><?php if ( isset($product_view['link']) && isset($product_view['title']) ){ ?><div class="data-control-id-2363 bd-producttitle-10"><a href="<?php echo $product_view['link']; ?>"><?php echo $product_view['title']; ?></a></div><?php } ?>
	
		<div class="data-control-id-2365 bd-productdesc-11">
    <?php
        if ( isset($product_view['desc']) && $product_view['desc'] ){
            $desc = apply_filters( 'woocommerce_short_description', $product_view['desc'] );
            $desc = wp_strip_all_tags( $desc, true );
            echo substr($desc, 0, 65) . preg_replace('/[\s!\?\.;][\s\S]*/', '...', substr($desc, 65));
        }
    ?>
</div></div></div>
</div>
	
		<div class="data-control-id-2477 
 col-md-5">
    <div class="bd-layoutcolumn-59"><div class="bd-vertical-align-wrapper"><div class="data-control-id-2435 bd-productprice-4">
    <?php
        if (isset($product_view['price'])) {
    ?>
    <span class="price"><?php
        echo theme_price_html(
            $product_view['price'], true, true,
            '<div class="data-control-id-2434 bd-pricetext-13">', '</div>',
            
 'data-control-id-2401 bd-label-13', 'data-control-id-2433 bd-container-32 bd-tagstyles',
            '<div class="data-control-id-2400 bd-pricetext-12">', '</div>',
            
 'data-control-id-2367 bd-label-12', 'data-control-id-2399 bd-container-31 bd-tagstyles');
?></span>
<?php
        }
?>
</div>
	
		<?php theme_product_buy('data-control-id-2440 bd-productbuy-3 bd-button'); ?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
                                </div>
                            </div>
                        <?php endwhile; // end of the loop. ?>
                    </div>
                  </div>
                </div>

                <div class="data-control-id-54745 bd-productsgridbar-37">
    <div class="bd-container-inner">
        <div id="pagination" class="data-control-id-2456 bd-productsgridpagination-1">
    <?php
            global $wp_query;

            if ( $wp_query->max_num_pages > 1 ) {
    echo preg_replace(
    array(
    '/<li(.*current)/',
    '/<ul class=\'page-numbers\'/',
    '/<li>/'
    ),
    array(
    '<li class="data-control-id-2454 data-control-id-2454 bd-paginationitem-4 active"$1',
    '<ul class="data-control-id-2455 data-control-id-2455 bd-pagination-4 pagination"',
    '<li class="data-control-id-2454 bd-paginationitem-4">'
    ),
    paginate_links( apply_filters( 'woocommerce_pagination_args', array(
    'base' 			=> str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ),
    'format' 		=> '',
    'current' 		=> max( 1, get_query_var('paged') ),
    'total' 		=> $wp_query->max_num_pages,
    'prev_text' 	=> '&larr;',
    'next_text' 	=> '&rarr;',
    'type'			=> 'list',
    'end_size'		=> 3,
    'mid_size'		=> 3
    ) ) ) );
    }
    ?>
</div>
    </div>
</div>

        <?php
                do_action('woocommerce_after_shop_loop');
            endif;
        ?>
        <div class="clear"></div>
    </div>
    
<?php
}
?>
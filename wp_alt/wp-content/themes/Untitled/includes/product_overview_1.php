<?php
function theme_product_overview() {
    global $product_overview_context;
    $product_overview_context = '.bd-productoverview';
?>
    <div class=" bd-productoverview">
        <?php 
			while ( have_posts() ) { 
				the_post();
				do_action('woocommerce_before_single_product');
    	        global $post, $product;
        	    $product_view = array();
            	$product_view['link']  = apply_filters('the_permalink', get_permalink());
            	$product_view['title'] = the_title('', '', false);
            	$product_view['price'] = theme_get_price_data($product);
            	$product_view['desc']  = $post->post_excerpt;
            	$product_view['image'] = woocommerce_get_product_thumbnail('shop_catalog', '', '');
        	?>
            	<div class=" bd-layoutcontainer-29">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class=" 
 col-md-12
 col-sm-24
 col-xs-24">
    <div class="bd-layoutcolumn-66"><div class="bd-vertical-align-wrapper"><?php if ( isset($product_view['link']) && isset($product_view['title']) ) : ?>
<h2 class=" bd-productoverviewtitle-1"><?php echo $product_view['title']; ?></h2>
<?php endif; ?>
	
		<div class=" bd-productimage-6">
    <div class="zoom-container">
    <?php if ( has_post_thumbnail() ) : ?>
        <a itemprop="image" href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" class="zoom" rel="thumbnails" title="<?php echo get_the_title( get_post_thumbnail_id() ); ?>">
        <?php
            global $post;
            remove_action('begin_fetch_post_thumbnail_html', '_wp_post_thumbnail_class_filter_add'); // disable 'wp-post-image' class
            echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ),  array('class' => ' bd-imagestyles') );
            add_action('begin_fetch_post_thumbnail_html', '_wp_post_thumbnail_class_filter_add');
        ?>
        </a>
    <?php else :  ?>
        <img class=' bd-imagestyles' src="<?php echo woocommerce_placeholder_img_src(); ?>" alt="Placeholder" />
    <?php endif; ?>
    </div>
</div>
<script>
    var image = jQuery('.bd-productimage-6').find('img');
    image.parentsUntil('.bd-productimage-6').andSelf().css('width', '100%').css('height', '100%');
</script>
	
		<?php
    $images = theme_get_product_thumbnails_data();
    $imagesCount = count($images);
    if ($imagesCount > 0) {
        $items = intval('5');
        $loop = 0;
        $innerStyle = '';
        $itemStyle = '';
        if ($imagesCount < $items && $items !== 0) {
            $innerStyle = 'style="width: ' . floor(100 / $items) * $imagesCount . '%;margin: 0 auto;"';
            $itemStyle = 'style="width:' . floor(100 / $imagesCount) . '%"';
        }
?>
<div class=" bd-imagethumbnails-1 carousel slide">
    <div class="carousel-inner" <?php echo $innerStyle ?>>
    <?php foreach ($images as $image): ?>
    <?php
        $classes = array('zoom');
        if ($loop == 0 || $loop % $items == 0)
            $classes[] = 'first';
        if (($loop + 1) % $items == 0)
            $classes[] = 'last';
    ?>
    <?php if ($loop % $items === 0): ?>
    <div class="item<?php if ($loop == 0): ?> active<?php endif ?>">
    <?php endif ?>
        <a class=" bd-productimage-7 <?php echo implode(' ', $classes); ?>" href="<?php echo $image['url']; ?>" title="<?php echo $image['title']; ?>" rel="smallImage:'<?php echo $image['preview']; ?>'" <?php echo $itemStyle; ?>>
            <img class=" bd-imagestyles" src="<?php echo $image['src']; ?>" />
        </a>
    <?php if ($loop === $imagesCount - 1 || ($loop + 1) % $items === 0): ?>
    </div>
    <?php endif; $loop++; ?>
    <?php endforeach ?>
</div>
<?php if ($imagesCount > $items): ?>
<div class="left-button">
    <a class=" bd-carousel" href="#">
        <span class=" bd-icon-13"></span>
    </a>
</div>

<div class="right-button">
    <a class=" bd-carousel" href="#">
        <span class=" bd-icon-13"></span>
    </a>
</div>
<?php endif ?>
</div>
<?php
    }
?></div></div>
</div>
	
		<div class=" 
 col-md-12
 col-sm-24
 col-xs-24">
    <div class="bd-layoutcolumn-67"><div class="bd-vertical-align-wrapper"><div class=" bd-productprice-5">
    <?php
        if (isset($product_view['price'])) {
    ?>
    <span class="price"><?php
        echo theme_price_html(
            $product_view['price'], true, true,
            '<div class=" bd-pricetext-15">', '</div>',
            
 ' bd-label-17', ' bd-container-36 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table',
            '<div class=" bd-pricetext-14">', '</div>',
            
 false, ' bd-container-35 bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-imagestyles bd-custom-table');
?></span>
<?php
        }
?>
</div>
	
		<div class=" bd-productrating-1">
    <?php $rating = round(theme_get_product_rating()); ?>
    <div class=" bd-rating-2">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <span class=" bd-icon-2 <?php if ($i <= $rating) echo ' active'; ?>"></span>
        <?php endfor; ?>
    </div>
</div>
	
		<div class=" bd-productdesc-13">
    <?php
        if ( isset($product_view['desc']) && $product_view['desc'] ){
            $desc = apply_filters( 'woocommerce_short_description', $product_view['desc'] );
            $desc = wp_strip_all_tags( $desc, true );
            echo substr($desc, 0, 65) . preg_replace('/[\s!\?\.;][\s\S]*/', '...', substr($desc, 65));
        }
    ?>
</div>
	
		<div class=" bd-productvariations-1">
    <?php
        global $product, $post;
        ob_start();
        switch($product->product_type) {
            case 'variable':
                woocommerce_variable_add_to_cart();
                break;
            case 'simple':
                woocommerce_simple_add_to_cart();
                break;
            case 'grouped':
                woocommerce_grouped_add_to_cart();
                break;
            case 'external':
                woocommerce_external_add_to_cart();
                break;
            case 'downloadable':
                do_action('woocommerce_before_add_to_cart_button');
                ?>
                <form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>">
                    <button></button>
                </form>
                <?php do_action('woocommerce_after_add_to_cart_button');
                break;
        }
        $content = ob_get_clean();
        $content = preg_replace('/<button.+button>/U', '', $content);
        $content = str_replace('type="number', 'type="text', $content);
        $content = str_replace('type="number', 'type="text', $content);
        $content = str_replace('class="input-text qty text"', 'class=" bd-bootstrapinput form-control qty" maxlength="12"', $content);
        echo $content;
    ?>
    <script>
        jQuery('.bd-productvariations-1 table.variations label').css('display', 'inline');
        jQuery('.bd-productvariations-1 table.variations a.reset_variations').each(function() {
            var reset_link = jQuery('<div>').append(jQuery(this).clone()).remove().html();
            this.remove();
            jQuery('.bd-productvariations-1 table.variations tbody').append('<tr><td></td><td>' + reset_link + '</td></tr>')
        });
    </script>
</div>
	
		<?php theme_product_buy(' bd-productbuy-4 bd-button-9'); ?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
	
		<?php
    $tabs = apply_filters( 'woocommerce_product_tabs', array() );

    if (is_null($tabs)) {
        $tabs = array('reviews' => array());
    }
    foreach(array('description', 'additional_information', 'reviews') as $tab) {
        if (!array_key_exists($tab, $tabs))
            continue;
        $tabs[$tab]['callback'] = 'theme_tab_' . $tab . '_2';
    }

    if ( ! empty( $tabs ) ) :
        ob_start();
        $count = 0;
        foreach ( $tabs as $key => $tab ) : ?>
            <li class="<?php if ($count == 0) echo 'active '; echo $key; ?>_tab  bd-menuitem-12">
                <a data-toggle="tab" href="#tab-<?php echo $key ?>2">
                    <span>
                        <?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?>
                    </span>
                </a>
            </li>
        <?php $count++; endforeach;
        $tabs_header = ob_get_clean();

        ob_start();
        $count = 0;
        foreach ( $tabs as $key => $tab ) : ?>
            <div class="tab-<?php echo $key; ?> tab-pane entry-content<?php if ($count == 0) echo ' active'; ?>" id="tab-<?php echo $key; ?>2">
                <?php call_user_func( $tab['callback'], $key, $tab ) ?>
            </div>
        <?php $count++; endforeach;
        $tabs_content = ob_get_clean();
    ?>

        <div class=" bd-tabinformationcontrol-2 tabbable" data-responsive="true">
            <ul class=" bd-menu-12 clearfix nav nav-tabs">
    <?php echo $tabs_header; ?>
</ul>
            <div class=" bd-container-37 bd-tagstyles tab-content">
    <?php echo $tabs_content; ?>
</div>
            <div class=" bd-accordion accordion">
    <div class=" bd-menuitem-8 accordion-item"></div>
    <div class=" bd-container-41 bd-tagstyles accordion-content"></div>
</div>
        </div>
<?php
    endif;
?>
			<?php 
				do_action('woocommerce_after_single_product');
			}
		?>
    </div>
<?php
    unset($product_overview_context);
}
<?php
function theme_blog() {
    global $post;
?>
    <div class=" bd-blog">
        
<?php
    
    if (have_posts()) { ?>
        <div class=" bd-grid-8">
          <div class="container-fluid">
            <div class="separated-grid row">
<?php       while (have_posts()) {
            the_post();

            $id = theme_get_post_id();
            $class = theme_get_post_class();
?>
                
                <div class="separated-item-28 col-md-24 ">
                
                    <div class="bd-griditem-28">
                        <article id="<?php echo $id; ?>" class=" bd-article-6 clearfix <?php echo $class; ?>">
    <?php
if (!is_page() || theme_get_meta_option($post->ID, 'theme_show_page_title')) {
    $title = get_the_title();
    if(!is_singular()) {
        $title = sprintf('<a href="%s" rel="bookmark" title="%s">%s</a>', get_permalink($post->ID), strip_tags($title), $title);;
    }
    if (!theme_is_empty_html($title)) {
?>
<h2 class=" bd-postheader-6">
    <div class="bd-container-inner">
        <?php echo $title; ?>
    </div>
</h2>
<?php
    }
}
?>
	
		<div class=" bd-layoutbox-16 clearfix">
    <div class="bd-container-inner">
        <?php echo theme_get_post_thumbnail(array('size' => 'full', 'imageClass' => ' bd-imagestyles', 'class' => ' bd-postimage-5')); ?>
	
		<div class=" bd-postcontent-10 bd-tagstyles">
    <div class="bd-container-inner">
        <?php echo(is_singular() ? theme_get_content() : theme_get_excerpt()); ?>
    </div>
</div>
	
		<?php
if($post && !is_singular()) {
    $text = "Continue reading...";
    if (!theme_is_empty_html($text)) { ?>
<a class="bd-postreadmore-6 bd-button " title="<?php echo strip_tags($text) ?>" href="<?php echo get_permalink($post->ID) ?>"><?php echo $text; ?></a>
<?php
    }
}
?>
    </div>
</div>
</article>
                        <?php
                        global $withcomments;
                        if (is_singular() || $withcomments){  ?>
                            <?php
    if (theme_get_option('theme_allow_comments')) {
        comments_template('/comments_5.php');
    }
?>
                        <?php } ?>
                    </div>
                </div>
<?php
        }
?>
                </div>
            </div>
        </div>
<?php
        } else {
?>
        <div class="bd-container-inner"><?php theme_404_content(); ?></div>
<?php
    }
?>
        <div class=" bd-blogpagination-5">
<?php
    if (!is_single() && function_exists('wp_page_numbers')) { // http://wordpress.org/extend/plugins/wp-page-numbers/
        wp_page_numbers();
    } elseif (!is_single() && function_exists('wp_pagenavi')) { // http://wordpress.org/extend/plugins/wp-pagenavi/
        wp_pagenavi();
    } else {
        $prev_link = theme_get_next_post_link('%link', '%title &raquo;');
        $next_link = theme_get_previous_post_link('%link', '&laquo; %title');
        if (!is_single()){
            //posts
            $code = 'return \'\';';
            add_filter( 'next_posts_link_attributes', create_function( '$a', $code ) );
            add_filter( 'previous_posts_link_attributes', create_function( '$a', $code ) );
            $prev_link = get_previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', THEME_NS));
            $next_link = get_next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', THEME_NS));
        }
?>
    <?php if ($prev_link || $next_link): ?>
    <ul class=" bd-pagination-6 pagination">
        <?php if ($next_link): ?>
            <li class=" bd-paginationitem-6">
                <?php echo $next_link; ?>
            </li>
        <?php endif ?>

        <?php if ($prev_link): ?>
            <li class=" bd-paginationitem-6">
                <?php echo $prev_link; ?>
            </li>
        <?php endif ?>
    </ul>
<?php endif ?>
<?php
    }
?>
</div>
    </div>
<?php

}
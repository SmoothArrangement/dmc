<?php
function theme_blog_4() {
    global $post;
?>
    <div class="data-control-id-1984 bd-blog-4">
        
            <?php
    if ( is_home() && 'page' == get_option('show_on_front') && get_option('page_for_posts') ){
        $blog_page_id = (int)get_option('page_for_posts');
        $title = '<a href="' . get_permalink($blog_page_id) . '" rel="bookmark" title="' . strip_tags(get_the_title($blog_page_id)) . '">' . get_the_title($blog_page_id) . '</a>';
    echo '<h2 class="data-control-id-1862 bd-container-23 bd-tagstyles">' . $title . '</h2>';
}
?>
<?php
    
    if (have_posts()) { ?>
        <div class="data-control-id-1830 bd-grid-7">
          <div class="container-fluid">
            <div class="separated-grid row">
<?php       while (have_posts()) {
            the_post();

            $id = theme_get_post_id();
            $class = theme_get_post_class();
?>
                
                <div class="separated-item-25 col-md-24 ">
                
                    <div class="bd-griditem-25">
                        <article id="<?php echo $id; ?>" class="data-control-id-1906 bd-article-5 clearfix <?php echo $class; ?>">
    <?php
if (!is_page() || theme_get_meta_option($post->ID, 'theme_show_page_title')) {
    $title = get_the_title();
    if(!is_singular()) {
        $title = sprintf('<a href="%s" rel="bookmark" title="%s">%s</a>', get_permalink($post->ID), strip_tags($title), $title);;
    }
    if (!theme_is_empty_html($title)) {
?>
<h2 class="data-control-id-1736 bd-postheader-5">
    <div class="bd-container-inner">
        <?php echo $title; ?>
    </div>
</h2>
<?php
    }
}
?>
	
		<?php echo theme_get_post_thumbnail(array('size' => 'full', 'imageClass' => 'data-control-id-1737 bd-imagestyles', 'class' => 'data-control-id-1738 bd-postimage-4')); ?>
	
		<div class="data-control-id-235136 bd-postcontent-9 bd-tagstyles">
    <div class="bd-container-inner">
        <?php echo(is_singular() ? theme_get_content() : theme_get_excerpt()); ?>
    </div>
</div>
</article>
                        <?php
                        global $withcomments;
                        if (is_singular() || $withcomments){  ?>
                            <?php
    if (theme_get_option('theme_allow_comments')) {
        comments_template('/comments_4.php');
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
        <div class="data-control-id-1868 bd-blogpagination-4">
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
    <ul class="data-control-id-1867 bd-pagination-8 pagination">
        <?php if ($next_link): ?>
            <li class="data-control-id-1866 bd-paginationitem-8">
                <?php echo $next_link; ?>
            </li>
        <?php endif ?>

        <?php if ($prev_link): ?>
            <li class="data-control-id-1866 bd-paginationitem-8">
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
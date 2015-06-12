<?php
function theme_blog_2() {
    global $post;
?>
    <div class="data-control-id-1519 bd-blog-2">
        
            <?php
    if ( is_home() && 'page' == get_option('show_on_front') && get_option('page_for_posts') ){
        $blog_page_id = (int)get_option('page_for_posts');
        $title = '<a href="' . get_permalink($blog_page_id) . '" rel="bookmark" title="' . strip_tags(get_the_title($blog_page_id)) . '">' . get_the_title($blog_page_id) . '</a>';
    echo '<h2 class="data-control-id-1397 bd-container-17 bd-tagstyles">' . $title . '</h2>';
}
?>
<?php
    
    if (have_posts()) { ?>
        <div class="data-control-id-1365 bd-grid-5">
          <div class="container-fluid">
            <div class="separated-grid row">
<?php       while (have_posts()) {
            the_post();

            $id = theme_get_post_id();
            $class = theme_get_post_class();
?>
                
                <div class="separated-item-34 col-md-24 ">
                
                    <div class="bd-griditem-34">
                        <article id="<?php echo $id; ?>" class="data-control-id-1441 bd-article-3 clearfix <?php echo $class; ?>">
    <?php
if (!is_page() || theme_get_meta_option($post->ID, 'theme_show_page_title')) {
    $title = get_the_title();
    if(!is_singular()) {
        $title = sprintf('<a href="%s" rel="bookmark" title="%s">%s</a>', get_permalink($post->ID), strip_tags($title), $title);;
    }
    if (!theme_is_empty_html($title)) {
?>
<h2 class="data-control-id-1245 bd-postheader-3">
    <div class="bd-container-inner">
        <?php echo $title; ?>
    </div>
</h2>
<?php
    }
}
?>
	
		<div class="data-control-id-1025346 bd-layoutbox-8 clearfix">
    <div class="bd-container-inner">
        <div class="data-control-id-1253 bd-posticondate-4">
    <span class="data-control-id-1252 bd-icon-39"><span><?php echo get_the_date(); ?></span></span>
</div>
	
		<div class="data-control-id-1262 bd-posticonauthor-5">
    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" title="<?php echo esc_attr(__('View all posts by ' . get_the_author(), THEME_NS)) ?>">
        <span class="data-control-id-1261 bd-icon-41"><span><?php echo get_the_author(); ?></span></span>
    </a>
</div>
	
		<div class="data-control-id-1271 bd-posticonedit-6">
    <?php if (current_user_can('edit_post', $post->ID)): ?>
    <?php edit_post_link('<span class="data-control-id-1270 bd-icon-43"><span>'.__('Edit', THEME_NS).'</span></span>', ''); ?>
    <?php endif; ?>
</div>
    </div>
</div>
	
		<div class="data-control-id-1025447 bd-layoutbox-10 clearfix">
    <div class="bd-container-inner">
        <?php echo theme_get_post_thumbnail(array('size' => 'full', 'imageClass' => 'data-control-id-1007943 bd-imagestyles', 'class' => 'data-control-id-1007944 bd-postimage-7')); ?>
	
		<div class="data-control-id-1315 bd-postcontent-2 bd-tagstyles">
    <div class="bd-container-inner">
        <?php echo(is_singular() ? theme_get_content() : theme_get_excerpt()); ?>
    </div>
</div>
    </div>
</div>
	
		<div class="data-control-id-1025625 bd-layoutbox-12 clearfix">
    <div class="bd-container-inner">
        <div class="data-control-id-1332 bd-posticontags-8">
    <?php $tags_list = get_the_tag_list('', ', '); ?>
    <?php if ($tags_list): ?>
    <span class="data-control-id-1331 bd-icon-45"><span><?php echo $tags_list; ?></span></span>
    <?php endif; ?>
</div>
	
		<div class="data-control-id-1323 bd-posticoncategory-7">
    <?php
        $categories = get_the_category_list(', ');
        if (theme_strlen($categories) > 0) : ?>
    <span class="data-control-id-1322 bd-icon-44"><span><?php echo get_the_category_list(', ');?></span></span>
    <?php endif ?>
</div>
    </div>
</div>
</article>
                        <?php
                        global $withcomments;
                        if (is_singular() || $withcomments){  ?>
                            <?php
    if (theme_get_option('theme_allow_comments')) {
        comments_template('/comments_2.php');
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
        <div class="data-control-id-1403 bd-blogpagination-2">
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
    <ul class="data-control-id-1402 bd-pagination-15 pagination">
        <?php if ($next_link): ?>
            <li class="data-control-id-1401 bd-paginationitem-15">
                <?php echo $next_link; ?>
            </li>
        <?php endif ?>

        <?php if ($prev_link): ?>
            <li class="data-control-id-1401 bd-paginationitem-15">
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
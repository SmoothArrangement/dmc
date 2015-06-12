<?php
/**
 *
 * comments_5.php
 *
 * The custom comments template. Used to display post or page comments and comment form.
 * 
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments_5.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die('Please do not load this page directly. Thanks!');
if (!function_exists('theme_comment_5')){
	function theme_comment_5($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		switch ($comment->comment_type) {
			case '' : ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div id="comment-<?php comment_ID() ?>">
    <div class="data-control-id-2224 bd-comment-5 <?php echo $comment->comment_type ?>">
        <div class="data-control-id-2110 bd-layoutcontainer-24">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row  ">
                <div class="data-control-id-2106 
 col-md-2">
    <div class="bd-layoutcolumn-55"><div class="bd-vertical-align-wrapper"><div class="data-control-id-2098 bd-commentavatar-5">
    <?php echo theme_get_avatar(array('class' => 'data-control-id-2090 bd-imagestyles'), true);  ?>
</div></div></div>
</div>
	
		<div class="data-control-id-2108 
 col-md-22">
    <div class="bd-layoutcolumn-56"><div class="bd-vertical-align-wrapper"><div class="data-control-id-2089 bd-commentmetadata-5 comment-meta commentmetadata">
    <?php printf(__('%s on ', THEME_NS), get_comment_author_link($comment->comment_ID)); ?>
    <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s at %2$s', THEME_NS), get_comment_date(), get_comment_time()); ?></a>
    <?php edit_comment_link(__('(Edit)', THEME_NS)); ?>
</div>
	
		<div class="data-control-id-2099 bd-commenttext-5 comment-body">
    <?php if ($comment->comment_approved == '0') : ?><em><?php _e('Your comment is awaiting moderation.', THEME_NS); ?></em><br /><?php endif; ?>
    <?php comment_text(); ?>
</div>
	
		<?php
if (!function_exists('theme_comment_reply_link_filter_5')) {
    function theme_comment_reply_link_filter_5($link) {
        return str_replace('class=\'', 'class=\'data-control-id-2103 bd-button ', $link);
    }
}
?>
<div class="data-control-id-2104 bd-commentreply-5 reply">
<?php
    add_filter('comment_reply_link', 'theme_comment_reply_link_filter_5');
    comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
    remove_filter('comment_reply_link', 'theme_comment_reply_link_filter_5');
    ?>
</div></div></div>
</div>
            </div>
        </div>
    </div>
</div>
    </div>
</div><?php
			break;
			case 'pingback' :
			case 'trackback' : ?>
<li class="post <?php echo $comment->comment_type ?>">
	<?php _e('Pingback:', THEME_NS); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', THEME_NS), ' ');
			break;
		}
	}
}
?>
<div class="data-control-id-2263 bd-comments-5">
	<div class="bd-container-inner">
	<?php if (post_password_required()) { ?>
		<div class="data-control-id-2222 bd-container-27 bd-tagstyles nocomments">
		<h2><?php _e('This post is password protected. Enter the password to view any comments.', THEME_NS) ?></h2>
		</div><?php
	} else {
		if (have_comments()) { ?>
			<div class="data-control-id-2222 bd-container-27 bd-tagstyles comments">
				<h2><?php printf(
						_n('One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), THEME_NS),
						number_format_i18n(get_comments_number()),
						get_the_title()
					); ?></h2>
			</div>
			<ul id="comments-list">
			<?php wp_list_comments('type=all&callback=theme_comment_5'); ?>
			</ul><?php
		}
		if (comments_open()) {
			?><?php
    if (!function_exists('theme_comment_form_defaults_filter_5')){
        function theme_comment_form_defaults_filter_5($defaults) {
            foreach(array('must_log_in', 'logged_in_as', 'comment_notes_before') as $key) {
                $defaults[$key] = str_replace('<p class="', '<label class="data-control-id-190873 bd-bootstraplabel ', $defaults[$key]);
                $defaults[$key] = str_replace('</p>', '</label>', $defaults[$key]);
            }
            $defaults['comment_notes_after'] = '<p class="form-allowed-tags">'
                . sprintf('<label class="data-control-id-190873 bd-bootstraplabel">' . __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ) . '</label>', ' <code>' . allowed_tags() . '</code>')
                . '</p>';
            return $defaults;
        }
        add_filter('comment_form_defaults', 'theme_comment_form_defaults_filter_5');
    }

    if (theme_get_option('theme_comment_use_smilies') && !function_exists('theme_comment_form_field_comment')) {
        function theme_comment_form_field_comment($form_field) {
            theme_include_lib('smiley.php');
            return theme_get_smilies_js() . '<p class="smilies">' . theme_get_smilies() . '</p>' . $form_field;
        }
        add_filter('comment_form_field_comment', 'theme_comment_form_field_comment');
    }
    ob_start();
    comment_form();
    echo str_replace(
        array(
            '<label',
            'class="comment-respond',
            '<h3',
            '</h3>',
            'type="text"',
            '<textarea',
            'type="submit"'
        ),
        array(
            '<label class="data-control-id-190873 bd-bootstraplabel"',
            'class="comment-respond data-control-id-2262 bd-commentsform-5',
            '<div class="data-control-id-2256 bd-container-28 bd-tagstyles"><h2',
            '</h2></div>',
            'type="text" class="data-control-id-2257 bd-bootstrapinput form-control"',
            '<textarea class="data-control-id-2257 bd-bootstrapinput form-control"',
            'class="data-control-id-2261 bd-button" type="submit"'
        ),
        ob_get_clean()
    );
?><?php
		}
	} ?>
	</div>
</div>
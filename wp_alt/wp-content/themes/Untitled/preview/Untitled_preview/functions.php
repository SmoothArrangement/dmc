<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('THEME_NAME', "Billion Themler theme");
global $wp_version;
define('WP_VERSION', $wp_version);
define('THEME_NS', 'twentyten');
define('THEME_LANGS_FOLDER', '/languages');
header('X-UA-Compatible: IE=edge');

remove_action('wp_head', 'wp_generator');

load_theme_textdomain(THEME_NS, get_template_directory() . THEME_LANGS_FOLDER);


if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding(get_bloginfo('charset'));
}
if (function_exists('mb_regex_encoding')) {
	mb_regex_encoding(get_bloginfo('charset'));
}

theme_include_lib('defaults.php');
theme_include_lib('misc.php');
theme_include_lib('wrappers.php');
theme_include_lib('sidebars.php');
theme_include_lib('navigation.php');
theme_include_lib('shortcodes.php');
theme_include_lib('widgets.php');
theme_include_lib('rating.php');
theme_include_lib('post_templates.php');

function theme_woocommerce_enabled() {
    global $woocommerce;
    return $woocommerce != null;
}

if (get_option('theme_show_comments_anywhere')) {
    global $withcomments;
    $withcomments = true;
}

add_action( 'wp_enqueue_scripts', 'theme_manage_woocommerce_styles', 99 );
function theme_manage_woocommerce_styles() {
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
    wp_dequeue_script( 'wc-single-product' );
}
add_action('wp_enqueue_scripts', 'theme_update_scripts_and_styles', 1000);
add_action('wp_head', 'theme_update_posts_styles', 1000);
add_action('wp_head', 'theme_header_image_script');
add_action('media_upload_image_header', 'wp_media_upload_handler');

function theme_header_rel_link() {
	if (theme_get_option('theme_header_clickable')):
		?><link rel='header_link' href='<?php echo esc_url(theme_get_option('theme_header_link')); ?>' /><?php
	endif;
}
add_action('wp_head', 'theme_header_rel_link');
add_action('login_head', 'theme_header_rel_link');

add_action('init', 'theme_editor_auto_login');
add_action('wp',   'theme_change_cart_page_template');

add_filter('locale', 'set_new_locale');

add_theme_support('post-thumbnails');
add_theme_support('nav-menus');
add_theme_support('automatic-feed-links');
add_theme_support('post-formats', array('aside', 'gallery'));
add_theme_support( 'woocommerce' );

function theme_wp_title( $title, $sep ) {
    global $paged, $page;
    if ( is_feed() ) {
        return $title;
    }
    if ( $paged >= 2 || $page >= 2 ) {
        $title = "$title $sep " . sprintf( __( 'Page %s', THEME_NS ), max( $paged, $page ) );
    }
    return $title;
}
add_filter( 'wp_title', 'theme_wp_title', 10, 2 );

function theme_autoinclude($folder){
    $path = get_stylesheet_directory() . '/' . $folder;

    if (!is_dir($path)) {
        return ;
    }

    if ($handle = opendir($path)) {
        while (($name = readdir($handle)) !== false) {
            if (!preg_match("#.php$#", $name)) {
                continue;
            }
            locate_template(array($folder . '/' . $name), true);
        }
        closedir($handle);
    }
}

theme_autoinclude('includes');

global $theme_error_messages;
$theme_error_messages = array();

function theme_add_error($message) {
    global $theme_error_messages;
    $theme_error_messages[] = $message;
}

function theme_add_errors() {
    global $current_screen, $theme_error_messages;
    if ($current_screen->parent_base === 'themes') {
        foreach($theme_error_messages as $message) {
            echo '<div class="error"><p>' . $message . '</p></div>';
        }
    }
}
add_action('admin_notices', 'theme_add_errors');

function theme_get_preview_theme_name($theme_name) {
    return $theme_name . ' (Preview)';
}

function theme_activation_function(){
    $base_template_dir = get_template_directory();
    $preview_template_dir = $base_template_dir . '_preview';

    $path = $base_template_dir . '/preview';
    $template = get_template();
    $preview_template = $template . '_preview';
    $theme_root = get_theme_root( $template );
    $inner_template = $path . '/' . $preview_template;

    if (!is_dir($path) || !is_dir($inner_template)) {
        return;
    }

    try {
        FilesHelper::test_permission($theme_root);

        FilesHelper::empty_dir($preview_template_dir, true);
        FilesHelper::rename($inner_template, $preview_template_dir);
        FilesHelper::empty_dir($path, true);
    } catch(PermissionDeniedException $e) {
        theme_add_error('You do not have permission to write to this directory: ' . $theme_root);
    }
}
add_action('after_switch_theme', 'theme_activation_function');


function theme_deactivation_function() {
    $theme_root = get_theme_root();
    $template = get_option('theme_switched');

    $base_template_dir = $theme_root . '/' . $template;
    $preview_template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');

    if (!is_dir($base_template_dir) || !is_dir($preview_template_dir)) {
        return ;
    }

    $path = $base_template_dir . '/preview/' . $template . '_preview';

    try {
        FilesHelper::test_permission($preview_template_dir);
        FilesHelper::empty_dir($base_template_dir . '/preview', true);
    } catch (PermissionDeniedException $e) {
        wp_die(str_replace('{folders}', '<ol><li>' . implode('</li><li>', theme_get_permissions_check_folders()) . '</li></ol>', $e->getExtendedMessage()));
    }

    FilesHelper::create_dir($base_template_dir . '/preview');
    FilesHelper::rename($preview_template_dir, $path);
}
add_action('switch_theme', 'theme_deactivation_function');

function theme_header_image_script() {
	$theme_header_image = theme_get_meta_option(get_queried_object_id(), 'theme_header_image');
	if ($theme_header_image) {
		?>
		<style>
			.bd-header {
				background-image : url(<?php echo $theme_header_image; ?>) !important;
				background-position : center center !important;
			}
		</style>
		<?php
	}
}

function theme_has_header_image() {
	return (bool) theme_get_meta_option(get_queried_object_id(), 'theme_header_image');
}
function theme_show_flash() {
	return (bool) theme_get_meta_option(get_queried_object_id(), 'theme_header_image_with_flash');
}

function theme_get_wc_template_path() {
    global $woocommerce;
    $template_path = method_exists($woocommerce, 'template_path') ? $woocommerce->template_path() : $woocommerce->template_url;
    return get_stylesheet_directory() . '/' . $template_path;
}

function theme_set_wc_template_path($located, $template_name) {
    // support old WC versions
    if ($template_name === 'cart/totals.php' || $template_name === 'cart/cart-totals.php') {
        $located = theme_get_wc_template_path() . 'cart/cart-totals-wrapper.php';
    }
    return $located;
}
add_filter('woocommerce_locate_template', 'theme_set_wc_template_path', 10, 2);

function theme_add_to_cart_message($message) {
    return str_replace('button wc-forward', '', $message);
}
add_filter('woocommerce_add_error', 'theme_add_to_cart_message');
add_filter('woocommerce_add_success', 'theme_add_to_cart_message');

if (is_admin()) {
	theme_include_lib('options.php');
	theme_include_lib('admins.php');
    theme_include_lib('export.php', 'export');
	function theme_add_option_page() {
		add_theme_page(__('Theme Options', THEME_NS), __('Theme Options', THEME_NS), 'edit_themes', basename(__FILE__), 'theme_print_options');
	}

	add_action('admin_menu', 'theme_add_option_page');
	add_action('sidebar_admin_setup', 'theme_widget_process_control');
	add_filter('widget_update_callback', 'theme_update_widget_additional');
	add_action('add_meta_boxes', 'theme_add_meta_boxes');
	add_action('save_post', 'theme_save_post');
	add_filter('wp_prepare_themes_for_js', 'theme_remove_customize_link');
    if (!file_exists(get_template_directory() . '/export/project.json')) {
        theme_add_error('<b>You can\'t edit this theme in Themler.</b><br>Most likely you are trying to edit the preview-theme. Please go to the themes page and activate the theme without "(Preview)".');
    }
	return;
}

function theme_preview_params_exists() {
    return isset($_GET) && isset($_GET['preview_iframe']) && isset($_GET['template'])
        && isset($_GET['stylesheet']) && isset($_GET['preview']);
}

function theme_is_preview_url($location) {
    $parse_location = parse_url($location);
    if (isset($parse_location['query'])){
        $query = explode('&' , $parse_location['query']);
        foreach ($query as $pair){
            $query_pair = explode('=' , $pair);
            if (isset($query_pair[0]) && (
                    strtolower($query_pair[0]) == 'preview'
                    || strtolower($query_pair[0]) == 'preview_iframe'
                    || strtolower($query_pair[0]) == 'template'
                    || strtolower($query_pair[0]) == 'stylesheet'))
            {
                return true;
            }
        }
    }
    return false;
}

add_filter('wp_redirect', 'theme_fix_woocommerce_redirect', 100);
function theme_fix_woocommerce_redirect($location) {

	if (!theme_preview_params_exists())
		return $location;

	if (!theme_is_preview_url($location)) {
        $anchor = '';
        $anchor_position = strpos($location, '#');
        if ($anchor_position !== false) {
            $anchor = substr($location, $anchor_position);
            $location = substr($location, 0, $anchor_position);
        }
        $parse_location = parse_url($location);
		$location .= (isset($parse_location['query']) ? '&' : '?') . 'preview=1&template=' . $_GET['template'] . '&stylesheet=' . $_GET['stylesheet'] . '&preview_iframe=1' . $anchor;
	}
	return $location;
}

function theme_fix_preview_redirect($redirect_url) {
    if (theme_preview_params_exists()) {
        $new_redirect_url = add_query_arg('preview', '1', $redirect_url);
        if (theme_is_preview_url($new_redirect_url)) {
            $redirect_url = $new_redirect_url;
        }
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'theme_fix_preview_redirect');

function theme_update_posts_styles() {
	global $wp_query;
	$res = '';
	if(!is_singular()) {
		$post_id = get_queried_object_id();
		if ($post_id == 0 && theme_is_home()) {
            $post_id = get_option('page_for_posts');
        }
		$res .= get_post_meta($post_id, 'theme_head', true);
	}
	while ($wp_query->have_posts()) {
		the_post();
		$post_id = theme_get_the_ID();
		$res .= get_post_meta($post_id, 'theme_head', true);
	}
	if (!empty($res)) {
		echo $res;
	}
	wp_reset_postdata();
}

function theme_get_option($name) {
	global $theme_default_options;
	$result = get_option($name);
	if ($result === false) {
		$result = theme_get_array_value($theme_default_options, $name);
	}
	return $result;
}

function theme_get_widget_meta_option($widget_id, $name) {
	global $theme_default_meta_options;
	if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
		return theme_get_array_value($theme_default_meta_options, $name);
	}
	$type = $matches[1];
	$id = $matches[2];
	$wp_widget = get_option('widget_' . $type);
	if (!$wp_widget || !isset($wp_widget[$id])) {
		return theme_get_array_value($theme_default_meta_options, $name);
	}
	if (!isset($wp_widget[$id][$name])) {
		$wp_widget[$id][$name] = theme_get_array_value(get_option($name), $widget_id, theme_get_array_value($theme_default_meta_options, $name));
	}
	return $wp_widget[$id][$name];
}

function theme_set_widget_meta_option($widget_id, $name, $value) {
	if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
		return;
	}
	$type = $matches[1];
	$id = $matches[2];
	$wp_widget = get_option('widget_' . $type);
	if (!$wp_widget || !isset($wp_widget[$id])) {
		return;
	}
	$wp_widget[$id][$name] = $value;
	update_option('widget_' . $type, $wp_widget);
}

function theme_get_meta_option($id, $name) {
	global $theme_default_meta_options;
	if (!is_numeric($id)) {
        return theme_get_array_value($theme_default_meta_options, $name);
    }
	$value = get_post_meta($id, '_' . $name, true);
	if ('' === $value) {
		$value = theme_get_array_value(get_option($name), $id, theme_get_array_value($theme_default_meta_options, $name));
		theme_set_meta_option($id, $name, $value);
	}
	return $value;
}

function theme_set_meta_option($id, $name, $value) {
	update_post_meta($id, '_' . $name, $value);
}

function theme_get_post_id() {
	$post_id = theme_get_the_ID();
	if ($post_id != '') {
		$post_id = 'post-' . $post_id;
	}
	return $post_id;
}

function theme_get_the_ID() {
	global $post;
	return $post->ID;
}

function theme_get_post_class() {
	return implode(' ', get_post_class());
}

function theme_include_lib($name, $dir = 'library') {
	locate_template(array($dir . '/' . $name), true);
}

function theme_get_post_thumbnail($args = array()) {
	global $post;

	$size = theme_get_array_value($args, 'size', array(theme_get_option('theme_metadata_thumbnail_width'), theme_get_option('theme_metadata_thumbnail_height')));
	$auto = theme_get_array_value($args, 'auto', theme_get_option('theme_metadata_thumbnail_auto'));
	$featured = theme_get_array_value($args, 'featured', theme_get_option('theme_metadata_use_featured_image_as_thumbnail'));
	$title = theme_get_array_value($args, 'title', '');
    $img_class = theme_get_array_value($args, 'imageClass', '');
    $link_class = theme_get_array_value($args, 'class', '');

	$result = '';

	if ($featured && (has_post_thumbnail())) {
		theme_ob_start();
		the_post_thumbnail($size, array('alt' => '', 'title' => $title, 'class' => $img_class));
		$result = theme_ob_get_clean();
	} elseif ($auto) {
		$attachments = get_children(array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
		if ($attachments) {
			$attachment = array_shift($attachments);
			$img = wp_get_attachment_image_src($attachment->ID, $size);
			if (isset($img[0])) {
				$result = '<img src="' . $img[0] . '" alt="" width="' . $img[1] . '" height="' . $img[2] . '" title="' . $title . '" class="'. $img_class .'" />';
			}
		}
	}
	if ($result !== '') {
		$result = '<a href="' . get_permalink($post->ID) . '" title="' . $title . '" class="'. $link_class . '">' . $result . '</a>';
	}
	return $result;
}

function theme_get_content($args = array()) {
	$more_tag = theme_get_array_value($args, 'more_tag', __('Continue reading <span class="meta-nav">&rarr;</span>', THEME_NS));
	theme_ob_start();
	the_content($more_tag);
	$content = theme_ob_get_clean();
	return $content . wp_link_pages(array(
				'before' => '<p><span class="page-navi-outer page-navi-caption"><span class="page-navi-inner">' . __('Pages', THEME_NS) . ': </span></span>',
				'after' => '</p>',
				'link_before' => '<span class="page-navi-outer"><span class="page-navi-inner">',
				'link_after' => '</span></span>',
				'echo' => 0
			));
}

function theme_get_excerpt($args = array()) {
    global $post;
    $more_tag = theme_get_array_value($args, 'more_tag', __('Continue reading <span class="meta-nav">&rarr;</span>', THEME_NS));
    $auto = theme_get_array_value($args, 'auto', theme_get_option('theme_metadata_excerpt_auto'));
    $all_words = theme_get_array_value($args, 'all_words', theme_get_option('theme_metadata_excerpt_words'));
    $min_remainder = theme_get_array_value($args, 'min_remainder', theme_get_option('theme_metadata_excerpt_min_remainder'));
    $allowed_tags = theme_get_array_value($args, 'allowed_tags',
        (theme_get_option('theme_metadata_excerpt_use_tag_filter')
            ? explode(',',str_replace(' ', '', theme_get_option('theme_metadata_excerpt_allowed_tags')))
            : null));
    $perma_link = get_permalink($post->ID);
    $more_token = '%%theme_more%%';
    $show_more_tag = false;
    $tag_disbalance = false;
    if (post_password_required($post)) {
        return get_the_excerpt();
    }
    if ($auto && has_excerpt($post->ID)) {
        $excerpt = get_the_excerpt();
        $show_more_tag = theme_strlen($post->post_content) > 0;
    } else {
        $excerpt = get_the_content($more_token);
        // hack for badly written plugins
        theme_ob_start();
        echo apply_filters('the_content', $excerpt);
        $excerpt = theme_ob_get_clean();
        global $multipage;
        if ($multipage && theme_strpos($excerpt, $more_token) === false) {
            $show_more_tag = true;
        }
        if (theme_is_empty_html($excerpt))
            return $excerpt;
        if ($allowed_tags !== null) {
            $allowed_tags = '<' . implode('><', $allowed_tags) . '>';
            $excerpt = strip_tags($excerpt, $allowed_tags);
        }
        if (theme_strpos($excerpt, $more_token) !== false) {
            $excerpt = str_replace($more_token, '', $excerpt);
            $show_more_tag = true;
        } elseif ($auto && is_numeric($all_words)) {
            $token = "%theme_tag_token%";
            $content_parts = explode($token, str_replace(array('<', '>'), array($token . '<', '>' . $token), $excerpt));
            $content = array();
            $word_count = 0;
            foreach ($content_parts as $part) {
                if (theme_strpos($part, '<') !== false || theme_strpos($part, '>') !== false) {
                    $content[] = array('type' => 'tag', 'content' => $part);
                } else {
                    $all_chunks = preg_split('/([\s])/u', $part, -1, PREG_SPLIT_DELIM_CAPTURE);
                    foreach ($all_chunks as $chunk) {
                        if ('' != trim($chunk)) {
                            $content[] = array('type' => 'word', 'content' => $chunk);
                            $word_count += 1;
                        } elseif ($chunk != '') {
                            $content[] = array('type' => 'space', 'content' => $chunk);
                        }
                    }
                }
            }

            if (($all_words < $word_count) && ($all_words + $min_remainder) <= $word_count) {
                $show_more_tag = true;
                $tag_disbalance = true;
                $current_count = 0;
                $excerpt = '';
                foreach ($content as $node) {
                    if ($node['type'] == 'word') {
                        $current_count++;
                    }
                    $excerpt .= $node['content'];
                    if ($current_count == $all_words) {
                        break;
                    }
                }
            }
        }
    }
    if ($show_more_tag && theme_get_option('theme_show_morelink')) {
        $excerpt = $excerpt . ' ' . str_replace(array('[url]', '[text]'), array($perma_link, $more_tag), theme_get_option('theme_morelink_template'));
    }
    if ($tag_disbalance) {
        $excerpt = force_balance_tags($excerpt);
    }
    return $excerpt;
}

function theme_get_search() {
	theme_ob_start();
	get_search_form();
	return theme_ob_get_clean();
}

function theme_is_home() {
	return (is_home() && !is_paged());
}

function theme_404_content($args = '') {
    $args = wp_parse_args($args, array(
            'error_title' => __('Not Found', THEME_NS),
            'error_message' => __('Apologies, but the page you requested could not be found. Perhaps searching will help.', THEME_NS)
        )
    );
    extract($args);
    echo '<h4>' . $error_title . '</h4>';
    echo '<p class="center">' . $error_message . '</p>';

    if (theme_get_option('theme_show_random_posts_on_404_page')) {
        theme_ob_start();
        echo '<h4 class="box-title">' . theme_get_option('theme_show_random_posts_title_on_404_page') . '</h4>';
        ?>
        <ul>
            <?php
            global $post;
            $rand_posts = get_posts('numberposts=5&orderby=rand');
            foreach ($rand_posts as $post) :
                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        echo theme_ob_get_clean();
    }
    if (theme_get_option('theme_show_tags_on_404_page')) {
        theme_ob_start();
        echo '<h4 class="box-title">' . theme_get_option('theme_show_tags_title_on_404_page') . '</h4>';
        wp_tag_cloud('smallest=9&largest=22&unit=pt&number=200&format=flat&orderby=name&order=ASC');
        echo theme_ob_get_clean();
    }
}

function theme_get_previous_post_link($format = '&laquo; %link', $link = '%title', $in_same_cat = false, $excluded_categories = '') {
	return theme_get_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true);
}

function theme_get_next_post_link($format = '%link &raquo;', $link = '%title', $in_same_cat = false, $excluded_categories = '') {
	return theme_get_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false);
}

function theme_get_adjacent_image_link($prev = true, $size = 'thumbnail', $text = false) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children(array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID')));

	foreach ($attachments as $k => $attachment)
		if ($attachment->ID == $post->ID)
			break;

	$k = $prev ? $k - 1 : $k + 1;

	if (isset($attachments[$k]))
		return wp_get_attachment_link($attachments[$k]->ID, $size, true, false, $text);
}

function theme_get_previous_image_link($size = 'thumbnail', $text = false) {
	$result = theme_get_adjacent_image_link(true, $size, $text);
	if ($result)
		$result = '&laquo; ' . $result;
	return $result;
}

function theme_get_next_image_link($size = 'thumbnail', $text = false) {
	$result = theme_get_adjacent_image_link(false, $size, $text);
	if ($result)
		$result .= ' &raquo;';
	return $result;
}

function theme_get_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ($previous && is_attachment())
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if (!$post)
		return;

	$title = strip_tags($post->post_title);

	if (empty($post->post_title))
		$title = $previous ? __('Previous Post', THEME_NS) : __('Next Post', THEME_NS);

	$title = apply_filters('the_title', $title, $post->ID);
	$short_title = $title;
	if (theme_get_option('theme_single_navigation_trim_title')) {
		$short_title = theme_trim_long_str($title, theme_get_option('theme_single_navigation_trim_len'));
	}
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="' . get_permalink($post) . '" title="' . esc_attr($title) . '" rel="' . $rel . '">';
	$link = str_replace('%title', $short_title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	return apply_filters("{$adjacent}_post_link", $format, $link);
}

function theme_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    switch ($comment->comment_type) {

    case '' :
?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <?php theme_ob_start(); ?>
            <div class="comment-author vcard">
                <?php echo theme_get_avatar(array('id' => $comment, 'size' => 48)); ?>
                <?php printf(__('%s <span class="says">says:</span>', THEME_NS), sprintf('<cite class="fn">%s</cite>', get_comment_author_link())); ?>
            </div>
            <?php if ($comment->comment_approved == '0') : ?>
            <em><?php _e('Your comment is awaiting moderation.', THEME_NS); ?></em>
            <br />
            <?php endif; ?>

            <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                <?php printf(__('%1$s at %2$s', THEME_NS), get_comment_date(), get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)', THEME_NS), ' '); ?>
            </div>

            <div class="comment-body"><?php comment_text(); ?></div>

            <div class="reply">
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
            </div>
            <?php echo '<div id="comment-'.get_comment_ID().'">' . theme_ob_get_clean() . '</div>'; ?>
<?php
        break;
    case 'pingback' :
    case 'trackback' :
?>
        <li class="post pingback">
            <?php theme_ob_start(); ?>
            <p><?php _e('Pingback:', THEME_NS); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', THEME_NS), ' '); ?></p>
<?php
            echo '<div class="' . $comment->comment_type . '">' . theme_ob_get_clean() . '</div>';
        break;
    }
}

function theme_replace_attr($subject, $attr, $value) {
    if ($value) {
        return preg_replace("/(" . $attr . "=)\'(.*?)\'/", '$1\'' . $value . '\'', $subject);
    } else {
        return preg_replace("/(" . $attr . "=)\'(.*?)\'/", '', $subject);
    }
}

function theme_get_avatar($args = '', $remove_size = false) {
	$args = wp_parse_args($args, array('class' => '', 'id' => false, 'size' => 96, 'default' => '', 'alt' => false, 'url' => false));
	extract($args);
	$result = get_avatar($id, $size, $default, $alt);
    if ($class) {
        $result = theme_replace_attr($result, 'class', $class);
    }
    if ($remove_size) {
        $result = theme_replace_attr($result, 'height', '');
        $result = theme_replace_attr($result, 'width', '');
    }
	if ($result && $url) {
        $result = '<a href="' . esc_url($url) . '">' . $result . '</a>';
	}
	return $result;
}

function theme_get_next_post() {
	static $ended = false;
	if (!$ended) {
		if (have_posts()) {
			the_post();
			get_template_part('content', get_post_format());
		} else {
			$ended = true;
		}
	}
}

function theme_ob_start() {
	ob_start();
}

function theme_ob_get_clean() {
	return ob_get_clean();
}

function theme_get_path(){
    $template = get_template();
    $theme_root = get_theme_root( $template );
    return $theme_root . '/' . $template;
}

function theme_print_content() {
    global $theme_content_function;
    if (!isset($theme_content_function)) {
        $theme_content_function = 'theme_blog';
    }
    if (function_exists($theme_content_function)) {
        call_user_func($theme_content_function);
        return;
    }
}

function theme_get_image_path($image) {
    $is_preview = isset($_GET['preview']);

    $template = get_template();
    $theme_root = get_theme_root($template);
    $template_dir = $theme_root . '/' . $template;
    $image_path = $template_dir . '/' . $image;

    if ($is_preview && !file_exists($image_path)) {
        return $base_image_url = preg_replace('/(.*)(_preview$)/', '$1', get_template_directory_uri()) . '/' . $image;
    }
    return get_template_directory_uri() . '/' . $image;
}

function theme_get_optimal_path($name, $ext){
    $suffix = '.min';
    $file = '/' . $name . '.' . $ext;
    $min_file = '/' . $name . $suffix . '.' . $ext;
    return (isset($_GET['preview']) || !file_exists(theme_get_path() . $min_file) || filemtime(theme_get_path() . $min_file) < filemtime (theme_get_path() . $file))
        ? $file : $min_file;
}

function theme_update_scripts_and_styles() {
    $template_url = str_replace( array( 'http:', 'https:' ), '', get_bloginfo('template_url', 'display'));
    $is_preview = isset($_GET['preview']);
    $version = wp_get_theme()->get('Version');
    wp_enqueue_script( 'jquery', false, array(), $version, 'all' );

    wp_register_style( 'theme-bootstrap',  $template_url . theme_get_optimal_path('bootstrap','css'), array(), $version, 'all' );
    wp_enqueue_style("theme-bootstrap");

    if ($is_preview && file_exists(theme_get_path() . '/style.preview.php')) {
        wp_register_style( 'theme-style', $template_url . '/style.preview.php', array('theme-bootstrap'), $version, 'all' );
    } else {
        wp_register_style( 'theme-style', $template_url . theme_get_optimal_path('style','css'), array('theme-bootstrap'), $version, 'all' );
    }
    wp_enqueue_style("theme-style");

    wp_register_script("theme-bootstrap", $template_url . '/bootstrap.min.js', array('jquery'), $version);
    wp_enqueue_script("theme-bootstrap");

    wp_register_script("theme-script", $template_url . '/script.js', array('jquery', 'theme-bootstrap'), $version);
    wp_enqueue_script("theme-script");

    if ($is_preview){
        wp_register_script("script.preview.js", $template_url . '/script.preview.js', array('jquery'), $version);
        wp_enqueue_script("script.preview.js");
    }

    if (theme_woocommerce_enabled() && is_checkout()) {
        wp_dequeue_script('wc-checkout');
        wp_enqueue_script('wc-checkout', $template_url . '/checkout.min.js', array('jquery'), false, true);
    }

    if (is_singular() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

function theme_editor_auto_login() {
    if(isset($_GET['editor_auto_login'])) {
        $_POST['log'] = $_GET['log'];
        $_POST['pwd'] = $_GET['pwd'];
    }
}

global $wp_locale;
if (isset($wp_locale)) {
    $wp_locale->text_direction = 'ltr';
}

function theme_get_price_html_to_text() {
    return '<span class="from">' . _x('To:', 'min_price', 'woocommerce') . ' </span>';
}

function theme_price_add_prefix($html) {
    return str_replace('<span class="amount">', '<span class="from">'.__('Price:', 'woocommerce').'</span><span class="amount">', $html);
}

function theme_woocommerce_variable_free_price_html($price, $product) {
    if ( $product->min_variation_price !== $product->max_variation_price ) {
        $full = '<div>' . $product->get_price_html_from_text() . __( 'Free!', 'woocommerce' ) . '</div>'
              . '<div>'. theme_get_price_html_to_text() . woocommerce_price($product->max_variation_price) . '</div>';
        $short = __( 'Free', 'woocommerce' ) . '<span class="amount">-</span>' . woocommerce_price($product->max_variation_price);
    } else {
        $full = __('Free!', 'woocommerce');
        $short = __('Free!', 'woocommerce');
    }
    return sprintf('<ins><full>%s</full><short>%s</short></ins>', $full, $short);
}

function theme_woocommerce_variable_price_html($price, $product) {
    if ( $product->min_variation_price !== $product->max_variation_price ) {
        $full = '<div>' . $product->get_price_html_from_text() . woocommerce_price($product->get_price()) . '</div>'
              . '<div>' . theme_get_price_html_to_text() . woocommerce_price($product->max_variation_price) . '</div>';
        $short = woocommerce_price($product->get_price()) . '<span class="amount">-</span>' . woocommerce_price($product->max_variation_price);
    } else {
        $short = woocommerce_price($product->get_price());
        $full = theme_price_add_prefix($short);
    }
    return sprintf('<ins><full>%s</full><short>%s</short></ins>', $full, $short);
}

function theme_woocommerce_simple_price_html($price, $product) {
    $short_new = woocommerce_price($product->get_price());
    $price = '<ins>'.
        '<short>' . $short_new . '</short>'.
        '<full>' . theme_price_add_prefix($short_new) . '</full></ins>';
    if ($product->is_on_sale() && $product->regular_price) {
        $short_old = is_numeric($product->regular_price) ? woocommerce_price($product->regular_price) : $product->regular_price;
        $price = '<del>'.
            '<short>' . $short_old . '</short>'.
            '<full>' . theme_price_add_prefix($short_old) . '</full></del>' . $price;
    }
    return $price;
}

function theme_woocommerce_variable_sale_price_html($price, $product) {
    $from_text = '';
    if ( ! $product->min_variation_price || $product->min_variation_price !== $product->max_variation_price )
        $from_text .= $product->get_price_html_from_text();
    $from = $product->min_variation_regular_price;
    $to = $product->get_price();
    return '<del><short>' . ( ( is_numeric( $from ) ) ? woocommerce_price( $from ) : $from ) . '</short>' .
                '<full>' . $from_text . ( ( is_numeric( $from ) ) ? woocommerce_price( $from ) : $from ) . '</full></del>' .

           '<ins><short>' . ( ( is_numeric( $to ) ) ? woocommerce_price( $to ) : $to ) . '</short>' .
                '<full>' . $from_text . ( ( is_numeric( $to ) ) ? woocommerce_price( $to ) : $to ) . '</full></ins>';
}

function theme_woocommerce_grouped_price_html($price, $product) {
    $child_prices = array();
    foreach ( $product->get_children() as $child_id )
        $child_prices[] = get_post_meta( $child_id, '_price', true );
    $child_prices = array_unique( $child_prices );
    if ( ! empty( $child_prices ) ) {
        $min_price = min( $child_prices );
        $max_price = max( $child_prices );
    } else {
        $min_price = '';
        $max_price = '';
    }
    if (sizeof($child_prices) > 1) {
        $full = '<div>' . $product->get_price_html_from_text() . woocommerce_price($min_price) . '</div>'
              . '<div>' . theme_get_price_html_to_text() . woocommerce_price($max_price) . '</div>';
        $short = woocommerce_price($min_price) . '<span class="amount">-</span>' . woocommerce_price($max_price);
    } else {
        $full = woocommerce_price($product->get_price());
        $short = woocommerce_price($product->get_price());
    }
    return sprintf('<ins><full>%s</full><short>%s</short></ins>', $full, $short);
}


function theme_get_price_data($product) {
    add_filter('woocommerce_grouped_price_html', 'theme_woocommerce_grouped_price_html', 10, 2);
    add_filter('woocommerce_variable_sale_price_html', 'theme_woocommerce_variable_sale_price_html', 10, 2);
    add_filter('woocommerce_sale_price_html', 'theme_woocommerce_simple_price_html', 10, 2);
    add_filter('woocommerce_price_html', 'theme_woocommerce_simple_price_html', 10, 2);
    add_filter('woocommerce_variable_price_html', 'theme_woocommerce_variable_price_html', 10, 2);
    add_filter('woocommerce_variable_free_sale_price_html', 'theme_woocommerce_variable_free_price_html', 10, 2);
    add_filter('woocommerce_variable_free_price_html', 'theme_woocommerce_variable_free_price_html', 10, 2);
    $price_data = $product->get_price_html();
    remove_filter('woocommerce_grouped_price_html', 'theme_woocommerce_grouped_price_html');
    remove_filter('woocommerce_variable_sale_price_html', 'theme_woocommerce_variable_sale_price_html');
    remove_filter('woocommerce_sale_price_html', 'theme_woocommerce_simple_price_html');
    remove_filter('woocommerce_price_html', 'theme_woocommerce_simple_price_html');
    remove_filter('woocommerce_variable_price_html', 'theme_woocommerce_variable_price_html');
    remove_filter('woocommerce_variable_free_sale_price_html', 'theme_woocommerce_variable_free_price_html');
    remove_filter('woocommerce_variable_free_price_html', 'theme_woocommerce_variable_free_price_html');
    return $price_data;
}

function theme_convert_price_data_to_html($html, $label, $amount) {
    $html = preg_replace($label ? '/<short.*short>/' : '/<full.*full>/', '', $html);
    $html = str_replace('<full>', '', $html);
    $html = str_replace('</full>', '', $html);
    $html = str_replace('<short>', '', $html);
    $html = str_replace('</short>', '', $html);
    if ($label)
        $html = str_replace('class="from"', 'class="'. $label .'"', $html);
    $html = str_replace('class="amount"', 'class="'. $amount .'"', $html);
    return $html;
}

function theme_price_html($price_data, $swap_old_regular, $show_old_price,
                          $old_price_start, $old_price_end, $old_price_label, $old_price_amount,
                          $regular_price_start, $regular_price_end, $regular_price_label, $regular_price_amount) {

    $price_html = $price_data;
    $old_price_html = '';
    if (preg_match('/<ins>(.*)<\/ins>/', $price_data, $ins_matches))
        $price_html = $ins_matches[1];
    if (preg_match('/<del>(.*)<\/del>/', $price_data, $del_matches))
        $old_price_html = $del_matches[1];

    $price_html = $price_html ? $regular_price_start . theme_convert_price_data_to_html($price_html, $regular_price_label, $regular_price_amount) . $regular_price_end : '';
    $old_price_html = $old_price_html ? $old_price_start . theme_convert_price_data_to_html($old_price_html, $old_price_label, $old_price_amount) . $old_price_end : '';

    if (!$show_old_price)
        $old_price_html = '';

    if ($swap_old_regular)
        return $old_price_html . $price_html;
    return $price_html . $old_price_html;
}

function theme_shop_products_per_page() {
    $items = theme_get_option( 'theme_shop_products_per_page' );
    return $items == 0 ? -1 : $items;
}
add_filter('loop_shop_per_page', 'theme_shop_products_per_page');

function set_new_locale( $lang ) {
    if (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }
    // return original language
    return $lang;
}

if (!function_exists('get_woocommerce_currencies')) {
    function get_woocommerce_currencies() {
        return array_unique(
            apply_filters( 'woocommerce_currencies',
                array(
                    'AUD' => __( 'Australian Dollars', 'woocommerce' ),
                    'BRL' => __( 'Brazilian Real', 'woocommerce' ),
                    'CAD' => __( 'Canadian Dollars', 'woocommerce' ),
                    'RMB' => __( 'Chinese Yuan', 'woocommerce' ),
                    'CZK' => __( 'Czech Koruna', 'woocommerce' ),
                    'DKK' => __( 'Danish Krone', 'woocommerce' ),
                    'EUR' => __( 'Euros', 'woocommerce' ),
                    'HKD' => __( 'Hong Kong Dollar', 'woocommerce' ),
                    'HUF' => __( 'Hungarian Forint', 'woocommerce' ),
                    'IDR' => __( 'Indonesia Rupiah', 'woocommerce' ),
                    'INR' => __( 'Indian Rupee', 'woocommerce' ),
                    'ILS' => __( 'Israeli Shekel', 'woocommerce' ),
                    'JPY' => __( 'Japanese Yen', 'woocommerce' ),
                    'KRW' => __( 'South Korean Won', 'woocommerce' ),
                    'MYR' => __( 'Malaysian Ringgits', 'woocommerce' ),
                    'MXN' => __( 'Mexican Peso', 'woocommerce' ),
                    'NOK' => __( 'Norwegian Krone', 'woocommerce' ),
                    'NZD' => __( 'New Zealand Dollar', 'woocommerce' ),
                    'PHP' => __( 'Philippine Pesos', 'woocommerce' ),
                    'PLN' => __( 'Polish Zloty', 'woocommerce' ),
                    'GBP' => __( 'Pounds Sterling', 'woocommerce' ),
                    'RON' => __( 'Romanian Leu', 'woocommerce' ),
                    'RUB' => __( 'Russian Ruble', 'woocommerce' ),
                    'SGD' => __( 'Singapore Dollar', 'woocommerce' ),
                    'ZAR' => __( 'South African rand', 'woocommerce' ),
                    'SEK' => __( 'Swedish Krona', 'woocommerce' ),
                    'CHF' => __( 'Swiss Franc', 'woocommerce' ),
                    'TWD' => __( 'Taiwan New Dollars', 'woocommerce' ),
                    'THB' => __( 'Thai Baht', 'woocommerce' ),
                    'TRY' => __( 'Turkish Lira', 'woocommerce' ),
                    'USD' => __( 'US Dollars', 'woocommerce' ),
                )
            )
        );
    }
}

function theme_get_languages() {
    return array('en_US' => __('English', THEME_NS),
                 'fr_FR' => __('French', THEME_NS),
                 'de_DE' => __('German', THEME_NS)
    );
}

function theme_get_woocommerce_currency_full_name($currency) {
    $currencies = get_woocommerce_currencies();
    return $currencies[$currency];
}

function theme_get_language_full_name($language) {
    $languages = theme_get_languages();
    return $languages[$language];
}

add_filter('woocommerce_currency', 'set_new_currency');

function set_new_currency( $currency ) {
    if (isset($_COOKIE['currency'])) {
        $new_currency = $_COOKIE['currency'];
        $currencies = get_woocommerce_currencies();
        if (isset($currencies[$new_currency]))
            $currency = $new_currency;
    }
    return $currency;
}

function theme_get_currency_title($textType, $showLabel, $showArrow) {
    $label = $showLabel ? __('Currency', THEME_NS) : '';
    $currency = get_woocommerce_currency();
    $symbol = get_woocommerce_currency_symbol($currency);

    if ($textType === 'noText')
        $value = $symbol;
    elseif ($textType === 'short')
        $value = $currency;
    elseif ($textType === 'full')
        $value = $symbol . ' ' . theme_get_woocommerce_currency_full_name($currency);

    if ($value && $label)
        $label .= ': ';
    $title = $label . $value;

    $title = '<span>' . $title . '</span>';
    if ($showArrow)
        $title .= ' <span class="caret"></span>';
    return $title;
}

function theme_get_language_title($textType, $showLabel, $showArrow) {
    $label = $showLabel ? __('Language', THEME_NS) : '';
    $language = get_locale();

    if ($textType === 'short')
        $value = preg_replace('/.+_(.+)/', '$1', $language);
    elseif ($textType === 'noText')
        $value = '';
    elseif ($textType === 'full')
        $value = theme_get_language_full_name($language);

    if ($value && $label)
        $label .= ': ';
    $title = $label . $value;
    $title = '<span>' . $title . '</span>';
    if ($showArrow)
        $title .= ' <span class="caret"></span>';
    return $title;
}

function theme_get_page_title() {
    $title = trim(wp_title('', false));
    if (is_home() && is_front_page()) {
        $title = trim(get_option('theme_home_page_title'));
        if (!$title)
            $title = get_bloginfo('name');
    } else if (!$title) {
        $title = get_bloginfo('name');
    } else if (theme_woocommerce_enabled() && is_shop()) {
        $shop_page = get_post( woocommerce_get_page_id( 'shop' ) );
        $title = apply_filters( 'the_title', ( $shop_page_title = get_option( 'woocommerce_shop_page_title' ) ) ? $shop_page_title : $shop_page->post_title );
    }
    return $title;
}

function theme_change_cart_page_template(){
    if (is_page()){
        global $post;
        if (function_exists('woocommerce_get_page_id') && woocommerce_get_page_id('cart') == $post->ID){
            if ( !is_page_template('cart.php') ) {
                update_post_meta($post->ID, '_wp_page_template', 'cart.php');
            }
        }
    }
}

function theme_product_buy($class = '') {
    global $product, $woocommerce, $product_overview_context;
    $context = isset($product_overview_context) ? $product_overview_context : false;
    if ( ! $product->is_purchasable() && ! in_array( $product->product_type, array( 'external', 'grouped' ) ) ) {
        ?>

    <?php
    } elseif ( ! $product->is_in_stock() ) {
        if (!$context) {
            ?>
            <a href="<?php echo apply_filters('out_of_stock_add_to_cart_url', get_permalink($product->id)); ?>"
               class="<?php echo $class; ?>"><?php echo apply_filters('out_of_stock_add_to_cart_text', __('Read More', 'woocommerce')); ?></a>
        <?php
        }
    } else {

        switch ( $product->product_type ) {
            case "variable" :
                $link 	= apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
                $label 	= apply_filters( 'variable_add_to_cart_text', __($context ? 'Add to cart' : 'Select options', 'woocommerce') );
                break;
            case "grouped" :
                $link 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
                $label 	= apply_filters( 'grouped_add_to_cart_text', __($context ? 'Add to cart' : 'View options', 'woocommerce') );
                break;
            case "external" :
                $link 	= apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
                $label 	= apply_filters( 'external_add_to_cart_text', __($context ? 'Add to cart' : 'Read More', 'woocommerce') );
                break;
            default :
                $link 	= apply_filters( 'add_to_cart_url', theme_get_server_url() . esc_url( $product->add_to_cart_url() . '&_wp_http_referer=' . urlencode($woocommerce->cart->get_cart_url()) ) );
                $label 	= apply_filters( 'add_to_cart_text', __('Add to cart', 'woocommerce') );
                break;
        }

        if ($context) {
            ?>
            <button onclick="jQuery('<?php echo $context; ?> form.cart').submit()" class="<?php echo $class; ?>">
                <?php _e('Add to cart', 'woocommerce'); ?>
            </button>
        <?php
        } else {
            ?>
            <a href="<?php echo $link; ?>" rel="nofollow" data-product_id="<?php echo $product->id; ?>" class="<?php echo $class; ?> add_to_cart_button product_type_<?php echo $product->product_type; ?>"><?php echo $label; ?></a>
        <?php
        }
    }
}

function theme_product_image($product_view, $class = ''){
        if ( isset($product_view['image']) ){
            $image = $product_view['image'];
            if (strpos($image, 'class') === false) {
                $image = str_replace('<img', '<img class=" ' . $class . ' "', $image);
            } else {
                $image = preg_replace('/class([ \t]*)=([ \t]*)([\"\'])/', 'class=$3 ' . $class . ' ', $image);
            }
            echo $image;
        }
}

function theme_get_product_thumbnails_data(){
    global $product;
    $attachments_ids = method_exists($product, 'get_gallery_attachment_ids') ? $product->get_gallery_attachment_ids() : array(get_post_thumbnail_id());
    $images = array();
    if ($attachments_ids) {
        foreach ( $attachments_ids as $key => $attachment_id ) {
            if ( get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) == 1 )
                continue;
            $images[] = array(
                'url' => wp_get_attachment_url($attachment_id),
                'title' => esc_attr(get_the_title($attachment_id)),
                'src' => theme_get_array_value(wp_get_attachment_image_src($attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), false), 0),
                'preview' => theme_get_array_value(wp_get_attachment_image_src($attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), false), 0)
            );
        }
    }
    return $images;
}

function theme_get_product_rating(){
    global $wpdb, $post;
    $rating = $wpdb->get_var("
            SELECT AVG(meta_value) FROM $wpdb->commentmeta
            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
            WHERE meta_key = 'rating'
            AND comment_post_ID = $post->ID
            AND comment_approved = '1'
        ");
    return $rating;
}

function theme_woocommerce_in_cart_product_thumbnail($image) {
    return str_replace('>', ' style="max-width:none;">', $image);
}
add_filter('woocommerce_in_cart_product_thumbnail', 'theme_woocommerce_in_cart_product_thumbnail');

function theme_remove_customize_link($prepared_themes){
    $current_theme = get_stylesheet();
    if ( isset( $prepared_themes[ $current_theme ] ) ) {
			$theme_data = &$prepared_themes[ $current_theme ];
			$theme_data['actions']['customize'] = str_replace('customize.php?theme=', 'themes.php?page=theme_editor&theme=', $theme_data['actions']['customize']);
			$theme_data['actions']['customize'] = str_replace('customize.php', 'themes.php?page=theme_editor', $theme_data['actions']['customize']);
			$preview_theme = $current_theme . '_preview';
			if ( isset( $prepared_themes[ $preview_theme ] ) ) {
                unset($prepared_themes[ $preview_theme ]);
			}
    }
    
    return $prepared_themes;
}

function theme_customize_preview_js() {
    wp_enqueue_script( 'theme-customizer', get_template_directory_uri() . '/theme-customizer.js', array(), '', true );
    wp_enqueue_style( 'theme-customizer', get_template_directory_uri() . '/theme-customizer.css');
}
add_action( 'customize_preview_init', 'theme_customize_preview_js' );
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering',30);
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function theme_woocommerce_params($woocommerce_params) {
    $woocommerce_params['ajax_url'] .= '?preview=' . $_GET['preview'] . '&template=' . $_GET['template'] . '&stylesheet=' . $_GET['stylesheet'] . '&preview_iframe=' . $_GET['preview_iframe'];
    return $woocommerce_params;
}

function theme_can_view_preview() {
    return isset($_GET['template']) && isset($_GET['preview']) && isset($_GET['preview_iframe']) && current_user_can('switch_themes');
}

if (theme_can_view_preview()) {
    add_filter('woocommerce_params', 'theme_woocommerce_params');
    add_filter('wc_checkout_params', 'theme_woocommerce_params');
    add_filter('wc_cart_params', 'theme_woocommerce_params');
    add_filter('wc_cart_fragments_params', 'theme_woocommerce_params');
    add_filter('wc_add_to_cart_params', 'theme_woocommerce_params');
}

function theme_add_to_cart_fragments($fragments) {
    if (!isset($_GET['preview']))
        return $fragments;
    $new_fragments = array();
    foreach($fragments as $key => $content) {
        if ($key !== 'div.widget_shopping_cart_content') {
            $new_fragments[$key] = preview_theme_ob_filter($content);
        }
    }
    return $new_fragments;
}

function theme_get_wc_nonce_field($action) {
    global $woocommerce;
    return method_exists($woocommerce, 'nonce_field') ? $woocommerce->nonce_field($action, true, false) : wp_nonce_field("woocommerce-$action");
}

function theme_get_footer_name($name) {
    for($id = 1; $id <= 4; $id++) {
        if ('footer'.$id === $name) {
            return $name;
        }
    }
    return 'footer1';
}

function theme_custom_menu_filter($args) {
    $args['items_wrap'] = '<ul id="%1$s">%3$s</ul>';
    return $args;
}

add_filter('wp_nav_menu_args', 'theme_custom_menu_filter');

if (isset($_GET['preview'])) {
 	function theme_preview_theme_ob_filter( $content ) {
 		return preg_replace_callback( "|(<a.*?href)(=[\"'])(.*?)([\"'].*?>)|", 'theme_preview_theme_ob_filter_callback', $content );
 	}
 	function theme_preview_theme_ob_filter_callback($matches) {
 		$link = $matches[3];
		if ( false !== strpos($link, 'preview=1') || 0 === strpos($link, '#') || 0 === strpos($link, '//') || false !== strpos($link, '://') && 0 !== strpos($link,  home_url())) {
 			return $matches[1] . ' ' . $matches[2] . $matches[3] . $matches[4];
 		}
 		return $matches[0];
 	}
 	ob_start( 'theme_preview_theme_ob_filter' );
}

function theme_catch_template($template) {
    if (isset($_REQUEST['custom_page'])) {
        $template_file = $_REQUEST['custom_page'];
        $custom_template = STYLESHEETPATH . '/' . $template_file;
        if (file_exists($custom_template)) {
            $template = $custom_template;
        }
    }
    return $template;
}

if (theme_can_view_preview()) {
    foreach(array('page', 'single', 'taxonomy', 'frontpage') as $template) {
        add_filter($template . '_template', 'theme_catch_template');
    }
}


function theme_get_selected_template($type) {
    $type = sanitize_title_with_dashes($type);
    if (theme_can_view_preview() && isset($_GET['custom_template'])) {
        return $_GET['custom_template'];
    }
    return theme_get_option('theme_template_' . get_option('stylesheet') . '_' . $type);
}

?>
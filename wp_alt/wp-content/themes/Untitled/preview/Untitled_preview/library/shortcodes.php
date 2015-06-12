<?php
/**
 *
 * shortcodes.php
 *
 * Used to add custom shortcodes.
 *
 * To add custom shortcode please use the following code:
 * add_shortcode("my_shortcode_name", "my_shortcode_func");
 * function my_shortcode_func($atts) { // your code here... }
 *
 * More detailed information about shortcodes: http://codex.wordpress.org/Shortcode_API
 *
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ThemeShotcodes {
    /*
        [column width_lg="6" width="8" width_sm="12" width_xs="6"] Your Content Here [/column]
        [one_half last] 1/2 [/one_half]
        [one_third] 1/3 [/one_third]
        [two_third last] 2/3 [/two_third]
        [one_fourth]  1/4 [/one_fourth]
        [three_fourth] 3/4 [/three_fourth]
    */
    public static function column($atts, $content = '') {
        extract(shortcode_atts(array(
            'width_lg' => '',
            'width' => '4',
            'width_sm' => '',
            'width_xs' => '',
            'css' => '',
            'last' => false
        ), $atts));

        if (false === $last && is_array($atts) && !empty($atts)) {
            foreach( $atts as $key => $value) {
                if (is_numeric($key) && 'last' ===  $value) {
                    $last = true;
                    break;
                }
            }
        }

        $classes = array();
        if (intval($width_lg) > 0) {
            $classes[] = 'col-lg-' . $width_lg;
        }
        if (intval($width) > 0) {
            $classes[] = 'col-md-' . $width;
        }
        if (intval($width_sm) > 0) {
            $classes[] = 'col-sm-' . $width_sm;
        }
        if (intval($width_xs) > 0) {
            $classes[] = 'col-xs-' . $width_xs;
        }
        $result = '<!--Column="' . implode(' ', $classes) . '"-->';
        if ($css !== '') {
            $result .= '<div style="' . esc_attr($css) .'">'.do_shortcode($content). '</div>';
        } else {
            $result .=  do_shortcode($content);
        }
        $result .=  '<!--/Column' . (false !== $last ? 'Last' : '') .  '-->';
        return $result;
    }
    public static function one_half($atts, $content = '') {
        $atts['width'] = "12";
        return ThemeShotcodes::column($atts, $content);
    }
    public static function one_third($atts, $content = '') {
        $atts['width'] = "8";
        return ThemeShotcodes::column($atts, $content);
    }
    public static function two_third($atts, $content = '') {
        $atts['width'] = "16";
        return ThemeShotcodes::column($atts, $content);
    }
    public static function one_fourth($atts, $content = '') {
        $atts['width'] = "6";
        return ThemeShotcodes::column($atts, $content);
    }
    public static function three_fourth($atts, $content = '') {
        $atts['width'] = "18";
        return ThemeShotcodes::column($atts, $content);
    }
    public static function existsCssProperty($property, $css) {
        $existsProperty = false;
        if ($css !== '') {
            $styles = explode(';', $css);
            foreach ($styles as $i => $style) {
                $parts = explode(':', $style);
                if ($property === trim($parts[0]) && count($parts) > 1) {
                    $existsProperty = true;
                }
            }
        }
        return $existsProperty;
    }
    public static $slidesCount;
    /*
    [slider css="" wide_slides="yes|no" wide_carousel="yes|no" interval="3000"]
        [slide css="" image="http:// | id" link="" linktarget=""]any slide content here[/slide]
    [/slider]
    */
    public static function slider($atts, $content='') {
        extract(shortcode_atts(array(
            'wide_slides' => 'yes',
            'wide_carousel' => 'yes',
            'carousel' => 'yes',
            'interval' => '3000',
            'indicators' => 'yes',
            'wide_indicators' => 'no',
            'indicators_position' => 'center bottom',
            'css' => '',
            'id' => ''
        ), $atts));
        if (!ThemeShotcodes::existsCssProperty('height', $css)) {
            $css = 'height:200px;' . $css;
        }
        if (!$id) {
            $id = uniqid('slider');
        }
        ThemeShotcodes::$slidesCount = 0;
        $content = do_shortcode($content);
        $before = '';
        $after = '';
        if ('no' === $wide_slides) {
            $before = '<div class="bd-container-inner">';
            $after = '</div>';
        }
        $content_indicators_style  = '';
        if ('yes' === $indicators) {
            $parts = explode(' ', $indicators_position);
            $align =  isset($parts[0]) ? $parts[0] : 'left';
            $vAlign = isset($parts[1]) ? str_replace('center', 'middle', $parts[1]) : 'top';
            $content_indicators_style = <<<EOL
    #$id .slider-indicators-wrapper {
        text-align: $align;
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        white-space: nowrap;
        pointer-events: none;
        z-index:20;
    }
    #$id .slider-indicators-wrapper:before {
        content: '';
        display: inline-block;
        height: 100%;
        width: 0;
        vertical-align: $vAlign;
    }
    #$id .slider-indicators-wrapper .bd-indicators {
        pointer-events: auto;
        vertical-align: $vAlign;
    }
EOL;
            $indicators = array();
            for($i = 0; $i < ThemeShotcodes::$slidesCount; $i++) {
                $indicators[] = sprintf('<li class="bd-menuitem-15' . (0 === $i ? ' active' : '') . '"><a href="#" data-target="#%s" data-slide-to="%s"> </a></li>', $id, $i);
            }
            $content_indicators = sprintf(
                '<div class="slider-indicators-wrapper"><ol class=" bd-indicators">%s</ol></div>',
                implode("", $indicators)
            );
            $before =  ('yes' === $wide_indicators) ?  $content_indicators . $before : $before . $content_indicators;
        }

        if ('yes' === $carousel) {
            $content_nav = <<<EOL
    <div class="left-button">
        <a class=" bd-carousel" href="#" role="button" data-slide="prev">
            <span class="bd-icon-13"></span>
        </a>
    </div>
    <div class="right-button">
        <a class=" bd-carousel" href="#" role="button" data-slide="next">
            <span class="bd-icon-13"></span>
        </a>
    </div>
EOL;
            $after =  ('yes' === $wide_carousel) ? $content_nav . $after : $after . $content_nav;
        }
        return <<<EOL
<style>
    #$id {
        $css
    }
    #$id .slide-link {
        position: absolute;
        top:0;
        bottom:0;
        left: 0;
        right: 0;
        z-index:10;
    }
    #$id .carousel-inner {
        height: 100%;
    }
    #$id .item {
        height:100%;
        background-size: cover;
        background-position: center top;
    }$content_indicators_style
    #$id .left-button {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 15;
    }
    #$id .right-button {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        z-index: 15;
    }
</style>
<div id="$id" class="carousel slide">
    $before
    <div class="carousel-inner">$content</div>
    $after
</div>
<script>
    if ('undefined' !== typeof initSlider) {
        initSlider('#$id', 'left-button', 'right-button', '.bd-carousel', '.bd-indicators', $interval, false, undefined, true);
    }
</script>
EOL;
    }
    public static function slide($atts, $content='') {
        extract(shortcode_atts(array(
            'image' => '',
            'title' => '',
            'link' => '',
            'linktarget' => '',
            'css' => ''
        ), $atts));
        ThemeShotcodes::$slidesCount++;
        $content = do_shortcode($content);
        if ('' !== $image) {
            if (intval($image) !== 0)
                $image = wp_get_attachment_url($image);
            $css = 'background-image:url(' . $image . ');' . $css;
        }
        $attr = array();
        $attr['class'] = 'item';
        $attr['style'] = $css;
        if ('' !== $title) {
            $attr['title'] = $title;
        }
        if ('' !== $link) {
            $attr['data-url'] = $link;
            if ('' !== $linktarget) {
                $attr['data-target'] = $linktarget;
            }
        }
        return sprintf('<div %s>%s</div>', theme_prepare_attr($attr), $content);
    }
    // [box css="" full_width="yes|no" content_width="yes|no"]content with shortcodes[/box]
    public static function box($atts, $content='') {
        extract(shortcode_atts(array(
            'css' => '',
            'full_width' => 'no',
            'content_width' => 'yes',
            'class_names' => ''
        ), $atts));
        $result = '';
        if ($full_width === 'yes') {
            $result .= '</div>';
        }
        $result .= '<div';
        if ($class_names !== '') {
            $result .= ' class="' . esc_attr($class_names) . '"';
        }
        if ($css !== '') {
            $result .= ' style="' . esc_attr($css) . '"';
        }
        $result .= '>';
        if ($content_width === 'yes') {
            $result .= '<div class="bd-container-inner">';
        }
        $result .= do_shortcode($content);
        if ($content_width === 'yes') {
            $result .= '</div>';
        }
        $result .= '</div>';
        if ($full_width === 'yes') {
            $result .= '<div class="bd-container-inner">';
        }
        return $result;
    }
    // Textgroup
    public static function textGroup($atts, $content='') {
        extract(shortcode_atts(array(
            'image' => '',
            'image_link' => '',
            'image_position' => 'left',
            'image_width' => '',
            'image_height' => '',
            'image_css' => '',

            'header' => '',
            'header_tag' => 'h4',
            'header_css' => '',

            'css' => ''
        ), $atts));

        $image_positions = array('left' => 'pull-left', 'right' => 'pull-right', 'top' => 'top', 'bottom' => 'bottom', 'middle' => 'middle');

        $headers = array('h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6');
        array_key_exists(strtolower($header_tag), $headers) ? $_header = $headers[strtolower($header_tag)] : $_header = 'h4';
        $_header = $header !== '' ? '<' . $_header . ' class="media-heading"' .
            ($header_css !== '' ? ' style="' . esc_attr($header_css) . '"' : '') . '>' . $header . '</' . $_header . '>' : '';

        $_ip = strtolower($image_position);
        $_i = '';
        if ($image !== '') {
            $_iWidth = $image_width !== '' ? ' width="' . esc_attr($image_width) . '"' : '';
            $_iHeight = $image_height !== '' ? ' height="' . esc_attr($image_height) . '"' : '';
            $_i = '<img class="bd-imagestyles media-object img-responsive"' .
                ($image_css !== '' ? ' style="' . esc_attr($image_css) . '"' : '')
                . ' src="' . esc_attr($image) . '"' . $_iWidth . $_iHeight . '/>';

            if (array_key_exists($_ip, $image_positions)) {
                if ($_ip === 'left' || $_ip === 'right') {
                    $_i = '<a class="' . $image_positions[$_ip] . '" href="' . esc_attr($image_link) . '">' . $_i . '</a>';
                } else {
                    $_i = '<a href="' . esc_attr($image_link) . '">' . $_i . '</a>';
                }
            }
        }

        $_c = '<div class="media"' . ($css !== '' ? ' style="' . esc_attr($css) . '"' : '') . '>';

        if ($_ip === 'middle') {
            return $_c . '<div class="media-body">' . $_header . $_i . do_shortcode($content) . '</div></div>';
        }

        $_content = '<div class="media-body">' . $_header . do_shortcode($content) . '</div>';

        if ($_ip !== 'bottom') {
            return $_c . $_i . $_content . '</div>';
        }

        return $_c . $_content . $_i . '</div>';
    }
    // Button
    public static function button($atts, $content='') {
        extract(shortcode_atts(array(
            'link' => '/',
            'type' => 'default',
            'style' => '',
            'size' => '',
            'icon' => ''
        ), $atts));

        $classNames = 'bd-button';
        $linkContent = $content;
        $styles = array('default' => 'btn-default', 'primary' => 'btn-primary', 'success' => 'btn-success',
            'info' => 'btn-info', 'warning' => 'btn-warning', 'danger' => 'btn-danger', 'link' => 'btn-link');
        $sizes = array('large' => 'btn-lg', 'small' => 'btn-sm', 'xsmall' => 'btn-xs');

        if ($type === 'bootstrap') {
            $classNames = 'btn';
            array_key_exists(strtolower($style), $styles) ? $classNames .= ' ' . $styles[strtolower($style)] : '';
            array_key_exists(strtolower($size), $sizes) ? $classNames .= ' ' . $sizes[strtolower($size)] : '';
        }

        if ($icon !== '') {
            $linkContent = '<span class="' . esc_attr($icon) . '">&nbsp;</span>' . $linkContent;
        }

        return '<a class="' . $classNames . '" href="' . esc_attr($link) . '">' . $linkContent . '</a>';
    }
    // Video
    public static function video($atts, $content='') {
        extract(shortcode_atts(array(
            'link' => '/',
            'autoplay' => 'no',
            'loop' => 'no',
            'title' => 'yes',
            'light_control_bar' => 'no',
            'show_control_bar' => 'show',
            'css' => ''
        ), $atts));

        $isYouTube = strrpos(esc_attr($link), 'youtube');
        $isVimeo = strrpos(esc_attr($link), 'vimeo');

        if ($isYouTube !== false) {
            list(, $id) = explode('=', esc_attr($link));
            list($id,) = explode('&', $id);
            $url = 'https://www.youtube.com/embed/' . $id . '?';

            if (esc_attr($autoplay) === 'yes')
                $url .= 'autoplay=1&';

            if (esc_attr($title) === 'no')
                $url .= 'showinfo=1&';

            if (esc_attr($light_control_bar) === 'yes')
                $url .= 'theme=light&';

            if (esc_attr($loop) === 'yes')
                $url .= 'loop=1&playlist=' . $id . '&';

            if (esc_attr($show_control_bar) === 'autohide')
                $url .= 'autohide=1&';
            else if (esc_attr($show_control_bar) === 'hide')
                $url .= 'controls=0&';

            $iframe = '<iframe src="' . $url . '"></iframe>';
        } else if ($isVimeo !== false) {
            $id = end(explode('/', esc_attr($link)));
            $url = 'https://player.vimeo.com/video/' . $id . '?';

            if (esc_attr($autoplay) === 'yes')
                $url .= 'autoplay=1&';

            if (esc_attr($title) === 'no')
                $url .= 'title=1&';

            if (esc_attr($light_control_bar) === 'yes')
                $url .= 'color=ffffff&';

            if (esc_attr($loop) === 'yes')
                $url .= 'loop=1';

            $iframe = '<iframe src="' . $url . '"></iframe>';
        }

        return '<div class="embed-responsive embed-responsive-16by9" style="' . $css . '">' . $iframe . '</div>';
    }
    // Google Map
    public static function googlemap($atts) {
        extract(shortcode_atts(array(
            'address' => '',
            'zoom' => '',
            'map_type' => '',
            'language' => '',
            'css' => 'height:300px;width:100%',
        ), $atts));

        $languages = array("eu", "ca", "hr", "cs", "da", "nl", "en", "fi", "fr", "de", "gl", "el", "hi", "id", "it", "ja", "no",
            "nn", "pt", "rm", "ru", "sr", "sk", "sl", "es", "sv", "th", "tr", "uk", "vi");

        if ($address !== '') {
            $address = '&q=' . $address;
        }

        if ($zoom !== ''){
            $num = (int) $zoom;
            if ($num>0){
                $zoom = '&z=' . $num;
            }
            else{
                $zoom = '';
            }
        }

        if ($map_type !== ''){
            switch ($map_type) {
                case "road":
                    $map_type = '&t=m';
                    break;
                case "satelite":
                    $map_type = '&t=k';
                    break;
                default:
                    $map_type = '';
            }
        }

        if ($language !== '' && in_array($language, $languages)){
            $language = '&hl=' . $language;
        }
        else{
            $language = '';
        }

        $divs = '<div style="' . esc_attr($css) . '"><div class="embed-responsive" style="height: 100%; width: 100%;">';
        $iframe = '<iframe class="embed-responsive-item" src="http://maps.google.com/maps?output=embed' . esc_attr($address) . esc_attr($zoom) . esc_attr($map_type) . esc_attr($language) . '"></iframe>';
        $divEnd = '</div>';

        return $divs . $iframe . $divEnd . $divEnd;
    }
    // anchor
    public static function anchor($atts) {
        extract(shortcode_atts(array(
            'name' => ''
        ), $atts));


        return '<div class="bd-anchor" id="' . esc_attr($name) . '"></div>';
    }
    public static function subscribe_rss() {
        return '<a class="button rss-subscribe" href="' . get_bloginfo('rss2_url') . '" title="' . __('RSS Feeds', THEME_NS) . '">' . __('RSS Feeds', THEME_NS) . '</a>';
    }
    // ads
    public static function advertisement($atts) {
        extract(shortcode_atts(array(
            'code' => 1,
            'align' => 'left',
            'inline' => 0
        ), $atts));
        $ad = theme_get_option('theme_ad_code_' . $code);
        if (!empty($ad)):
            $ad = '<div class="ad align' . esc_attr($align) . '">' . $ad . '</div>';
            if (!$inline)
                $ad .= '<div class="cleared"></div>';
            return $ad;
        else:
            return '<p class="error"><strong>[ad]</strong> ' . sprintf(__("Empty ad slot (#%s)!", THEME_NS), esc_attr($code)) . '</p>';
        endif;
    }
    public static function go_to_top() {
        return sprintf('<a class="button" href="#">' . __('Top', THEME_NS) . '</a>');
    }
    // login
    public static function login_link() {
        if (is_user_logged_in())
            return sprintf('<a class="login-link" href="%1$s">%2$s</a>', admin_url(), __('Site Admin', THEME_NS));
        else
            return sprintf('<a class="login-link" href="%1$s">%2$s</a>', wp_login_url(), __('Log in', THEME_NS));
    }
    // blog title
    public static function blog_title() {
        return '<span class="blog-title">' . get_bloginfo('name') . '</span>';
    }
    // validate xhtml
    public static function validate_xhtml() {
        return '<a class="button valid-xhtml" href="http://validator.w3.org/check?uri=referer" title="Valid XHTML">XHTML 1.1</a>';
    }
    // validate css
    public static function validate_css() {
        return '<a class="button valid-css" href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3" title="Valid CSS">CSS 3.0</a>';
    }
    // current year
    public static function current_year() {
        return date('Y');
    }
    public static function rss_url() {
        return get_bloginfo('rss2_url', 'raw');
    }
    public static function rss_title() {
        return sprintf(__('%s RSS Feed', THEME_NS), get_bloginfo('name'));
    }
    public static function template_url() {
        return get_bloginfo('template_url', 'display');
    }
    public static function post_link($atts) {
        extract(shortcode_atts(array(
            'name' => '/',
        ), $atts));
        $raw_name = $name;
        $type = 'page';
        if(strpos($name, '/Blog%20Posts/') === 0) {
            $name = substr($name, strlen('/Blog%20Posts/'));
            $type = 'post';
        }
        $target = get_page_by_path($name, OBJECT, $type);
        if(null !== $target) {
            return get_permalink($target->ID);
        } else {
            return $raw_name;
        }
    }
    public static function search() {
        theme_ob_start();
        get_search_form();
        return theme_ob_get_clean();
    }

    public static $inRow = false;

    public static function filter($content) {
        global $shortcode_tags;
        $orig_shortcode_tags = $shortcode_tags;
        $shortcode_tags = array();
        add_shortcode('column', 'ThemeShotcodes::column');
        add_shortcode('one_half', 'ThemeShotcodes::one_half');
        add_shortcode('one_third', 'ThemeShotcodes::one_third');
        add_shortcode('two_third', 'ThemeShotcodes::two_third');
        add_shortcode('one_fourth', 'ThemeShotcodes::one_fourth');
        add_shortcode('three_fourth', 'ThemeShotcodes::three_fourth');
        add_shortcode('slider', 'ThemeShotcodes::slider');
        add_shortcode('slide', 'ThemeShotcodes::slide');
        add_shortcode('box', 'ThemeShotcodes::box');
        $content = do_shortcode($content);
        $shortcode_tags = $orig_shortcode_tags;
        $content = preg_replace('/(<!--\/Column)(?:Last){0,1}(-->)(?!.*<!--\/Column)/s', '$1Last$2', $content, 1);
        ThemeShotcodes::$inRow = false;
        return  preg_replace_callback(
            '/<!--Column(="[^"]*?")-->(.*?)<!--\/Column(Last){0,1}-->/s', 'ThemeShotcodes::callback', $content);
    }
    public static function callback($matches)
    {
        $result = '';
        if (!ThemeShotcodes::$inRow) {
            $result .= '<div class="row">';
            ThemeShotcodes::$inRow = true;
        }
        $result .= '<div class' . theme_get_array_value($matches, 1, '') . '>' . theme_get_array_value($matches, 2, '') .'</div>';
        if (theme_get_array_value($matches, 3)) {
            $result .= '</div>';
            ThemeShotcodes::$inRow = false;
        }
        return $result;
    }
    public static function init()
    {
        add_filter('widget_text', 'do_shortcode'); // Allow [SHORTCODES] in Widgets
        add_filter('the_content', 'ThemeShotcodes::filter', 7);
        add_filter('widget_text', 'ThemeShotcodes::filter', 7);
        add_shortcode('video', 'ThemeShotcodes::video');
        add_shortcode('googlemap', 'ThemeShotcodes::googlemap');
        add_shortcode('text_group', 'ThemeShotcodes::textGroup');
        add_shortcode('button', 'ThemeShotcodes::button');
        add_shortcode('anchor', 'ThemeShotcodes::anchor');
        add_shortcode('year', 'ThemeShotcodes::current_year');
        add_shortcode('rss', 'ThemeShotcodes::subscribe_rss');
        add_shortcode('ad', 'ThemeShotcodes::advertisement');
        add_shortcode('top', 'ThemeShotcodes::go_to_top');
        add_shortcode('login_link', 'ThemeShotcodes::login_link');
        add_shortcode('blog_title', 'ThemeShotcodes::blog_title');
        add_shortcode('xhtml', 'ThemeShotcodes::validate_xhtml');
        add_shortcode('css', 'ThemeShotcodes::validate_css');
        add_shortcode('rss_url', 'ThemeShotcodes::rss_url');
        add_shortcode('rss_title', 'ThemeShotcodes::rss_title');
        add_shortcode('template_url', 'ThemeShotcodes::template_url');
        add_shortcode('post_link', 'ThemeShotcodes::post_link');
        add_shortcode('search', 'ThemeShotcodes::search');
    }
}
ThemeShotcodes::init();
?>
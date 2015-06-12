<?php

global $theme_templates, $theme_template_types;
$theme_templates = array();
$theme_template_types = array();

function register_template($name, $url, $add_preview_params = true, $is_absolute = false) {
    global $theme_templates;
    if (isset($theme_templates[$name]))
        throw new Exception('Template ' . $name . ' is already exists');
    $path = $is_absolute ? '' : get_option('home');
    if ($add_preview_params) {
        $template = get_template() . '_preview';
        $url = add_query_arg(array('preview' => '1', 'template' => $template, 'stylesheet' => $template, 'preview_iframe' => 1, 'TB_iframe' => 'true'), $path . $url);
    }
    $theme_templates[$name] = $url;
}

function allow_template_duplicate($name) {
    $GLOBALS['theme_template_types'][$name] = true;
}

class PageTemplatesHelper {

    public $default_pattern = '([_a-z0-9-]+)';
    public $patterns;

    function __construct() {
        $this->patterns = array(
            '{id}'        => '(\d+)',
            '{slug}'      => $this->default_pattern,
            '{taxonomy}'  => $this->default_pattern,
            '{term}'      => $this->default_pattern,
            '{nicename}'  => $this->default_pattern,
            '{post_type}' => $this->default_pattern
        );
    }

    public function match($filename, $pattern) {
        $pattern = '#^' . str_replace(array_keys($this->patterns), array_values($this->patterns), $pattern) . '.php$#i';
        preg_match($pattern, $filename, $matches);
        if (empty($matches)) {
            return false;
        }
        return $matches;
    }

    function get_any_page_id() {
        $exclude_pages = array();
        $exclude_pages[] = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0; // front page
        $exclude_pages[] = (int) get_option( 'page_for_posts' ); // page for posts
        if (theme_woocommerce_enabled()) {
            $exclude_pages[] = (int) woocommerce_get_page_id('cart'); // cart page
            $exclude_pages[] = (int) woocommerce_get_page_id('shop'); // shop page
        }

        $all_pages = get_pages(array(
            'sort_column' => 'ID',
            'exclude' => implode(', ', $exclude_pages),
            'number' => 1
        ));
        if (isset($all_pages[0]))
            return $all_pages[0]->ID;
        return 1;
    }

    function get_any_post_id() {
        $posts = get_posts(array('number' => 1));
        if (isset($posts[0]))
            return $posts[0]->ID;
        return 1;
    }

    function get_sample_page_by_slug($slug) {
        $pages = get_posts(array(
            'name' => $slug,
            'post_type'   => 'page',
            'numberposts' => 1,
            'orderby'     => 'name'
        ));
        if (empty($pages))
            return false;
        return get_permalink($pages[0]->ID);
    }

    function get_sample_page_by_id($id) {
        $post = get_post($id);
        if (empty($post))
            return false;
        return get_permalink($id);
    }

    function get_sample_category_by_slug($slug) {
        $cat = get_category_by_slug($slug);
        if (empty($cat))
            return false;
        return get_category_link($cat->cat_ID);
    }

    function get_sample_category_by_id($id) {
        $cat = get_category($id);
        if (empty($cat))
            return false;
        return get_category_link($cat->cat_ID);
    }

    function get_sample_category() {
        $categories = get_categories(array('number' => 1));
        if (empty($categories))
            return false;
        $cat = $categories[0];
        return get_category_link($cat->cat_ID);
    }

    function get_sample_tag_by_slug($slug) {
        $tags = get_tags(array(
            'slug' => $slug,
            'number' => 1
        ));
        $tag = $tags[0]; // ??
        if (empty($tag))
            return false;
        return get_tag_link($tag->term_id);
    }

    function get_sample_tag_by_id($id) {
        $tag = get_tag($id);
        if (empty($tag))
            return false;
        return get_tag_link($tag->term_id);
    }

    function get_sample_tag() {
        $tags = get_tags(array('number' => 1));
        if (empty($tags))
            return false;
        $tag = $tags[0];
        return get_tag_link($tag->term_id);
    }

    function get_sample_author_by_id($id) {
        $posts = get_posts(array(
            'author' => $id,
            'numberposts' => 1,
            'orderby' => 'name'
        ));
        if (empty($posts))
            return false;
        return get_permalink($posts[0]->ID);
    }

    function get_sample_author_by_name($name) {
        $user = get_user_by('slug', $name);
        if (empty($user))
            return false;
        return $this->get_sample_author_by_id($user->ID);
    }

    function get_sample_single() {
        return $this->get_sample_post_by_type('');
    }

    function get_sample_archive() {
        return $this->get_sample_page_date();
    }

    function get_sample_post_by_type($type) {
        $posts = get_posts(array(
            'post_type'   => $type,
            'numberposts' => 1
        ));
        if (empty($posts))
            return false;
        return get_permalink($posts[0]->ID);
    }

    function get_sample_page_date() {
        $posts = get_posts(array(
            'post_type'   => 'post',
            'numberposts' => 1,
            'orderby'     => 'date'
        ));
        if (empty($posts))
            return false;
        $post = $posts[0];
        return get_month_link(get_the_date('Y', $post->ID), get_the_date('m', $post->ID));
    }

    function get_sample_taxonomy($taxonomy = '', $term = '') {
        if (empty($term)) {
            if (empty($taxonomy)) {
                $custom_taxonomies = get_taxonomies(array('public' => true, '_builtin' => false));
                if (empty($custom_taxonomies)) {
                    $taxonomy = 'category';
                } else {
                    $custom_taxonomies = array_keys($custom_taxonomies);
                    $taxonomy = $custom_taxonomies[0];
                }
            }
            $terms = get_terms($taxonomy, array('number' => 1));
            if (!empty($terms)) {
                $term = $terms[0]->slug;
            }
        }
        return get_term_link($term, $taxonomy);
    }

    function get_sample_page_search() {
        $title = get_the_title($this->get_any_page_id());
        return home_url() . '?s=' . urlencode($title);
    }

    public function get_sample_post($filename, $post_meta_value) {
        $posts = get_posts(array(
            'post_type'   => 'post',
            'meta_key'    => 'theme_post_template',
            'meta_value'  => $post_meta_value,
            'numberposts' => 1,
            'orderby'     => 'name'
        ));
        if (!empty($posts)) {
            return add_query_arg(array('custom_page' => $filename), get_permalink($posts[0]->ID));
        }
        return add_query_arg(array('default' => 'true', 'custom_page' => $filename), get_permalink($this->get_any_post_id()));
    }

    public function get_sample_page($filename) {

        $exclude_pages[] = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0; // front page
        $exclude_pages[] = (int) get_option( 'page_for_posts' );                                            // page for posts
        if (theme_woocommerce_enabled()) {
            $exclude_pages[] = (int) woocommerce_get_page_id('cart'); // cart page
            $exclude_pages[] = (int) woocommerce_get_page_id('shop'); // shop page
        }

        $pages = get_posts(array(
            'post_type'   => 'page',
            'meta_key'    => '_wp_page_template',
            'meta_value'  => $filename,
            'numberposts' => 1,
            'exclude' => implode(', ', $exclude_pages),
            'orderby'     => 'name'
        ));
        if (!empty($pages)) {
            return get_permalink($pages[0]->ID);
        }

        $url = '';
        if (false !== ($r = $this->match($filename, 'search')) && ($url = $this->get_sample_page_search())) {

        }
        elseif (false !== ($r = $this->match($filename, 'date')) && ($url = $this->get_sample_page_date())) {

        }
        elseif (false !== ($r = $this->match($filename, 'page-{slug}')) && ($url = $this->get_sample_page_by_slug($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'page-{id}')) && ($url = $this->get_sample_page_by_id($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'category-{slug}')) && ($url = $this->get_sample_category_by_slug($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'category-{id}')) && ($url = $this->get_sample_category_by_id($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'category')) && ($url = $this->get_sample_category())) {

        }
        elseif (false !== ($r = $this->match($filename, 'tag-{slug}')) && ($url = $this->get_sample_tag_by_slug($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'tag-{id}')) && ($url = $this->get_sample_tag_by_id($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'tag')) && ($url = $this->get_sample_tag())) {

        }
        elseif (false !== ($r = $this->match($filename, 'author-{nicename}')) && ($url = $this->get_sample_author_by_name($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'author-{id}')) && ($url = $this->get_sample_author_by_id($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'single-{post_type}')) && ($url = $this->get_sample_post_by_type($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'single')) && ($url = $this->get_sample_single())) {

        }
        elseif (false !== ($r = $this->match($filename, 'archive')) && ($url = $this->get_sample_archive())) {

        }
        elseif (false !== ($r = $this->match($filename, 'taxonomy-{taxonomy}-{term}')) && ($url = $this->get_sample_taxonomy($r[1], $r[2]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'taxonomy-{taxonomy}')) && ($url = $this->get_sample_taxonomy($r[1]))) {

        }
        elseif (false !== ($r = $this->match($filename, 'taxonomy')) && ($url = $this->get_sample_taxonomy())) {

        }

        if ($url) {
            return add_query_arg(array('custom_page' => $filename), $url);
        }

        return add_query_arg(array('default' => 'true', 'custom_page' => $filename), get_permalink($this->get_any_page_id()));
    }
}

global $pageTemplatesHelper;
$pageTemplatesHelper = new PageTemplatesHelper();

?>
<?php

class ProviderLog
{
    private static $_log = array();

    private static function prepareKey($key){
        return '[PHP] ' . $key;
    }

    public static function start($key = '')
    {
        ProviderLog::$_log[] = array(
            'key' => ProviderLog::prepareKey($key),
            'type' => 'start',
            'time' => microtime(true) * 1000
        );
    }

    public static function end($key = '')
    {
        $key = ProviderLog::prepareKey($key);
        ProviderLog::$_log[] = array(
            'key' =>  $key,
            'type' => 'end',
            'time' => microtime(true) * 1000
        );
    }

    public static function getLog() {
        $key = ProviderLog::prepareKey('WordPress Core Load');
        array_unshift(ProviderLog::$_log, array(
            'key' => $key,
            'type' => 'start',
            'time' => 0
        ));
        ProviderLog::$_log[] = array(
            'key' => $key,
            'type' => 'end',
            'time' => timer_stop(0, 3) * 1000
        );
        return ProviderLog::$_log;
    }
}


if(!function_exists('theme_woocommerce_enabled')) {
    function theme_woocommerce_enabled() {
        global $woocommerce;
        return $woocommerce != null;
    }
}

if(!function_exists('theme_fdm_enabled')) {
    function theme_fdm_enabled() {
        global $fdm_controller;
        return $fdm_controller != null;
    }
}

function getSessionDomain() {
    $key = 'domain';
    $default_domain = 'http://billionthemler.com';
    $query_domain = isset($_GET[$key]) ? $_GET[$key] : false;
    $domain = false;
    $base_upload_dir = wp_upload_dir();
    if (false !== $base_upload_dir['error']) {
        die('Upload folder error!');
    }
    $domains_path = FilesHelper::normalize_path($base_upload_dir['basedir']). '/domains.json';
    $json = null;
    if(!file_exists($domains_path)) {
        if (false === $query_domain) { return $default_domain; }
    } else {
        $content = file_get_contents($domains_path);
        if (false !== $content ) {
            $json = json_decode($content, true);
        }
    }
    if (!is_array($json)) {
        $json = array();
    }
    $theme = get_template();
    if (array_key_exists($theme, $json)) {
        $domain = $json[$theme];
    }
    if (false !== $query_domain && $domain !== $query_domain) {
        $domain = $query_domain;
        $json[$theme] = $domain;
        if (false === file_put_contents($domains_path, json_encode($json), LOCK_EX))
            throw new PermissionDeniedException($domains_path);
    }
    return (false !== $domain) ? $domain  : $default_domain;
}

function theme_get_templates($use_preview = true) {
    global $theme_templates, $theme_template_types;

    $theme_root = get_template_directory();
    $preview_theme_root = $theme_root . '_preview';

    $templates_path = $theme_root . '/export/templates.php';
    $preview_templates_path = $preview_theme_root . '/export/templates.php';

    if ($use_preview && file_exists($preview_templates_path)) {
        $templates_path = $preview_templates_path;
        $theme_root = $preview_theme_root;
    }
    require_once($theme_root . '/export/templatesHelper.php');
    require_once($templates_path);

    $files = FilesHelper::enumerate_files($theme_root, false);
    $used_files = array();
    foreach($files as $file) {
        $info = pathinfo($file['path']);
        if ($info['extension'] === 'php') {
            $used_files[] = $info['filename'];
        }
    }

    return array(
        'urls' => $theme_templates,
        'types' => $theme_template_types,
        'used_files' => $used_files
    );
}

function getThemeVersions(){
    global $wp_version, $woocommerce;
    $wc_version = '';
    if (theme_woocommerce_enabled())
        $wc_version = $woocommerce->version;

    return array('wordpress' => $wp_version, 'woocommerce' => $wc_version);
}

// deprecated
function getThemeTemplates(){
    $templates = array();

    $templates['product_url'] = '';
    $query_args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $query = new WP_Query( $query_args );
    if ( $query->have_posts() ) {
        while ($query->have_posts()) : $query->the_post();
            global $post;
            $templates['product_url'] = 'p=' . $post->ID . '&';
            break;
        endwhile;
    }

    $templates['blogpage_url'] = '';
    if ( 'page' == get_option('show_on_front') && get_option('page_for_posts') ){
        $templates['blogpage_url'] = 'page_id=' . get_option('page_for_posts') . '&';
    }

    $templates['singlepost_url'] = '';
    $all_posts = get_posts(array('order' => 'ASC'));
    if ( isset($all_posts[0])){
        $single_post = $all_posts[0];
        $templates['singlepost_url'] = 'p=' . $single_post->ID . '&';
    }

    $categories = get_categories(array(
        'child_of'   => 0,
        'orderby'    => 'id',
        'order'      => 'ASC',
        'number'     => 1
    ));
    if (empty($categories)) {
        $templates['category_id'] = '1';
    } else {
        $categories = array_values($categories);
        $templates['category_id'] = $categories[0]->cat_ID;
    }

    $pages = array();
    $pages[] = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0; // front page
    $pages[] = (int) get_option( 'page_for_posts' ); // page for posts
    if (theme_woocommerce_enabled()){
        $cart_page  = (int) woocommerce_get_page_id('cart');
        $templates['cart_url'] = 'p=' . $cart_page . '&';
        $pages[] = $cart_page;
        $pages[] = (int) woocommerce_get_page_id('shop'); // shop page
    }

    $templates['page_url'] = '';
    $all_pages = get_pages( array( 'sort_column' => 'ID', 'exclude' => implode(', ', $pages) ) );
    if ( isset($all_pages[0])){
        $single_page = $all_pages[0];
        $templates['page_url'] = 'page_id=' . $single_page->ID . '&';
    }

    if (theme_fdm_enabled()) {
        // menu
        $res_url = '';
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        foreach ((array) $menus as $key => $menu) {
            $items = wp_get_nav_menu_items($menu);
            foreach ((array) $items as $key2 => $item) {
                if ($item->object === 'fdm-menu' && strpos($item->url, '?') > 0) {
                    $res_url = substr ($item->url, strpos($item->url, '?') + 1) . '&';
                    break;
                }
            }
            if ($res_url !== '') {
                break;
            }
        }
        $templates['fdm_menu_url'] = $res_url;

        // menu item
        $menu_item_query_args = array(
            'post_type'      => 'fdm-menu-item',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        $query = new WP_Query( $menu_item_query_args );
        if ( $query->have_posts() ) {
            while ($query->have_posts()) : $query->the_post();
                $menu_item_link = get_permalink();
                if (strpos($menu_item_link, '?') > 0){
                    $templates['fdm_menu_item_url'] = substr ($menu_item_link, strpos($menu_item_link, '?') + 1) . '&';
                }
                break;
            endwhile;
        }

    }

    return $templates;
}

function setThemeHashes($current_theme_path, $data = array()){
    ProviderLog::start('setThemeHashes');
    $hashesfile = $current_theme_path . '/export/hashes.json';
    ProviderLog::start('load');
    $hashes_content = file_exists($hashesfile) ? file_get_contents($hashesfile) : '';
    ProviderLog::end('load');
    if (false === $hashes_content)
        throw new PermissionDeniedException($hashesfile);
    ProviderLog::start('decode');
    $hashes = json_decode($hashes_content, true);
    ProviderLog::end('decode');
    ProviderLog::start('process');
    foreach ($data as $file => $content) {
        if ('[DELETED]' === $content) {
            if (isset($hashes[$file]))
                unset($hashes[$file]);
        } else {
            $hashes[$file] = $content;
        }
    }
    ProviderLog::end('process');
    ProviderLog::start('encode');
    $hashes_content = json_encode($hashes);
    ProviderLog::end('encode');
    ProviderLog::start('save');
    if (false === file_put_contents($hashesfile, $hashes_content))
        throw new PermissionDeniedException($hashesfile);
    ProviderLog::end('save');
    ProviderLog::end('setThemeHashes');
}

function getThemeHashes($current_theme_path){
    ProviderLog::start('getThemeHashes');
    $hashesfile = $current_theme_path . '/export/hashes.json';
    ProviderLog::start('load');
    $content = file_exists($hashesfile) ? file_get_contents($hashesfile) : '{}';
    ProviderLog::end('load');
    if (false === $content)
        throw new PermissionDeniedException($hashesfile);
    ProviderLog::start('decode');
    $result = json_decode($content);
    ProviderLog::end('decode');
    ProviderLog::end('getThemeHashes');
    return $result;
}

function setThemeCache($current_theme_path, $data = array()){
    ProviderLog::start('setThemeCache');
    $cachefile = $current_theme_path . '/export/cache.json';
    ProviderLog::start('load');
    $cache_content = file_exists($cachefile) ? file_get_contents($cachefile) : '';
    ProviderLog::end('load');
    if (false === $cache_content)
        throw new PermissionDeniedException($cachefile);
    ProviderLog::start('decode');
    $cache = json_decode($cache_content, true);
    ProviderLog::end('decode');
    ProviderLog::start('process');
    if (is_array($data)) {
        foreach ($data as $control => $storage) {
            if (!is_array($storage))
                continue;

            foreach ($storage as $file => $content) {
                if ('[DELETED]' === $content) {
                    if (isset($cache[$control]) && isset($cache[$control][$file]))
                        unset($cache[$control][$file]);
                } else {
                    $cache[$control][$file] = $content;
                }
            }
        }
    }
    ProviderLog::end('process');
    ProviderLog::start('encode');
    $cache_content = json_encode($cache);
    ProviderLog::end('encode');
    ProviderLog::start('save');
    if (false === file_put_contents($cachefile, $cache_content))
        throw new PermissionDeniedException($cachefile);
    ProviderLog::end('save');
    ProviderLog::end('setThemeCache');
}

function getThemeCache($current_theme_path){
    ProviderLog::start('getThemeCache');
    $cachefile = $current_theme_path . '/export/cache.json';
    $content = file_exists($cachefile) ? file_get_contents($cachefile) : '{}';
    if (false === $content)
        throw new PermissionDeniedException($cachefile);
    $result = json_decode($content);
    ProviderLog::end('getThemeCache');
    return $result;
}

function getThemeProject($current_theme_path){
    ProviderLog::start('getThemeProject');
    $projectfile = $current_theme_path . '/export/project.json';
    $project = array();
    if (file_exists($projectfile)) {
        $content = file_get_contents($projectfile);
        if (false === $content)
            throw new PermissionDeniedException($projectfile);
        $project = json_decode($content, true);
    }
    if(is_array($project['project_data']['project'])) {
        $project['project_data']['project'] = json_encode($project['project_data']['project']);
    }
    ProviderLog::end('getThemeProject');
    return $project;
}

function setThemeProject($current_theme_path, $project){
    ProviderLog::start('setThemeProject');
    $projectfile = $current_theme_path . '/export/project.json';
    if (false === file_put_contents($projectfile, json_encode($project)))
        throw new PermissionDeniedException($projectfile);
    ProviderLog::end('setThemeProject');
}

function setThemeProjectData($current_theme_path, $project_data){
    $project = getThemeProject($current_theme_path);
    $project['project_data'] = $project_data;
    setThemeProject($current_theme_path, $project);
}

function getMaxRequestSize()
{
    $postSize = toBytes(ini_get('post_max_size'));
    $uploadSize = toBytes(ini_get('upload_max_filesize'));
    $memorySize = toBytes(ini_get('memory_limit'));

    return min($postSize, $uploadSize, $memorySize);
}

function toBytes($str) {
    $str = strtolower(trim($str));

    if ($str) {
        switch ($str[strlen($str) - 1]) {
            case 'g':
                $str *= 1024;
            case 'm':
                $str *= 1024;
            case 'k':
                $str *= 1024;
        }
    }

    return intval($str);
}

function get_memory_limit() {
    if (!function_exists('ini_get'))
        return -1;
    return toBytes(ini_get('memory_limit'));
}

function theme_get_preview_changed_files()
{
    ProviderLog::start('theme_get_preview_changed_files');
    $base_template_dir = get_template_directory();
    $changed_files_file = $base_template_dir . '/export/preview_changed_files.json';
    $changed_files = null;
    if (is_file($changed_files_file)) {
        $changed_files_file_content = file_get_contents($changed_files_file);
        if ($changed_files_file_content) {
            $changed_files = json_decode($changed_files_file_content, true);
        }
    }
    ProviderLog::end('theme_get_preview_changed_files');
    return $changed_files;
}

function theme_get_preview_relative_path($path) {
    $preview_template_dir = FilesHelper::normalize_path(get_template_directory() . '_preview');
    $path = FilesHelper::normalize_path($path);
    if (substr($path, 0, strlen($preview_template_dir)) === $preview_template_dir) {
        $path = substr($path, strlen($preview_template_dir));
    }
    return $path;
}

function theme_set_preview_changed_files($changed_files) {
    ProviderLog::start('theme_set_preview_changed_files');
    $base_template_dir = get_template_directory();
    $changed_files_file = $base_template_dir . '/export/preview_changed_files.json';
    $_changed_files = $changed_files;
    $changed_files = array();
    foreach($_changed_files as $path) {
        $changed_files[] = theme_get_preview_relative_path($path);
    }
    $changed_files_file_content = json_encode(array_unique($changed_files));
    if (false === file_put_contents($changed_files_file, $changed_files_file_content))
        throw new PermissionDeniedException($changed_files_file);
    ProviderLog::end('theme_set_preview_changed_files');
}

function theme_is_valid_name($name) {
    return !validate_file($name) && preg_replace('|[^a-z0-9_]|i', '', $name) === $name;
}

function theme_get_permissions_check_folders() {
    $theme_root = get_theme_root(get_template());
    $base_upload_dir = wp_upload_dir();
    $upload_dir = $base_upload_dir['basedir'];
    return array($theme_root, $upload_dir);
}

function theme_out_of_memory_handler($cannot_allocate) {
    if ($cannot_allocate) {
        wp_die(<<<EOL
            <h3>PHP Memory Configuration Error</h3>

            <p>Themler requires at least 64Mb of PHP memory. Please increase your PHP memory to continue.
            For more information, please check this <a href="http://answers.billiondigital.com/articles/5826/out-of-memory" target="_blank">link</a>.</p>
EOL
        );
    } else {
        $current_memory = get_memory_limit() / 1024 / 1024 . 'Mb';
        wp_die(<<<EOL
            <h3>PHP Memory Configuration Error</h3>

            <p>Themler requires at least 64Mb of PHP memory (you have "$current_memory"). Please increase your PHP memory to continue.
            For more information, please check this <a href="http://answers.billiondigital.com/articles/5826/out-of-memory" target="_blank">link</a>.</p>
EOL
        );
    }
}

//http://stackoverflow.com/questions/2726524/can-you-unregister-a-shutdown-function
function theme_memory_limit_shutdown() {
    $error = error_get_last();
    if ($error && $error['type'] === E_ERROR && !isset($GLOBALS['memory_test_passed'])) {
        theme_out_of_memory_handler(true);
    }
}

function theme_test_memory_size() {
    // try to allocate 16Mb
    $alloc_bytes = 16 * 1024 * 1024;
    register_shutdown_function('theme_memory_limit_shutdown');

    $tmp = @str_repeat('.', $alloc_bytes);
    unset($tmp);
    $GLOBALS['memory_test_passed'] = true;

    return true;
}

function theme_check_memory_limit($test_alloc_memory = false) {
    $need_memory_size = 64 * 1024 * 1024;
    $memory = get_memory_limit();

    // can't retrieve memory limit option
    if (-1 == $memory)
        return;

    // try to increase limit
    if ($memory < $need_memory_size) {
    if(!function_exists('ini_set'))
            theme_out_of_memory_handler(false);

    $ret = ini_set('memory_limit', '64M');
    if (!$ret)
            theme_out_of_memory_handler(false);
    }

    // check real limits
    if ($test_alloc_memory)
        theme_test_memory_size();
}
?>
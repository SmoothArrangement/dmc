<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$base_template_dir = get_template_directory();
load_template($base_template_dir . '/export/Chunk.php');
load_template($base_template_dir . '/export/PreviewHelper.php');
load_template($base_template_dir . '/export/filesHelper.php');
load_template($base_template_dir . '/export/editorHelper.php');
load_template($base_template_dir . '/export/themeHelper.php');
if (file_exists($base_template_dir . '/export/archive.php')) {
    load_template($base_template_dir . '/export/archive.php');
}
function theme_add_billion_menu() {
    global $menu;
    $menu_slug = 'themes.php?page=theme_editor';
    $capability = 'edit_themes';
    $menu['58.91'] = array( '', $capability, 'separator-billion', '', 'wp-menu-separator billion' );
    $menu['58.95'] = array( __('Billion Theme', THEME_NS), $capability, $menu_slug, '', 'menu-top menu-icon-billion', 'menu-billion', 'div' );
}
add_action('_network_admin_menu', 'theme_add_billion_menu');
add_action('_user_admin_menu', 'theme_add_billion_menu');
add_action('_admin_menu', 'theme_add_billion_menu');

function theme_print_billion_menu_styles() {
    ?>
    <style>
        #adminmenu .menu-icon-billion div.wp-menu-image {
            background-image: url('<?php  esc_url( get_template_directory_uri() ) ; ?>/images/static/billon-themes.png');
            background-position:50% 55%;
            background-repeat: no-repeat;
        }

        #adminmenu .menu-icon-billion:hover div.wp-menu-image,
        #adminmenu .menu-icon-billion.wp-has-current-submenu div.wp-menu-image,
        #adminmenu .menu-icon-billion.current div.wp-menu-image {
            background-image: url('<?php  esc_url( get_template_directory_uri() ) ; ?>/images/static/billon-themes-h.png');
        }
    </style>
<?php
}
add_action('admin_print_styles', 'theme_print_billion_menu_styles');

function theme_add_billion_submenu() {
    global $submenu;
    $menu_slug = 'themes.php?page=theme_editor';
    $capability = 'edit_themes';
    $submenu[$menu_slug][10] = array( __( 'Billion Themler', THEME_NS), $capability, $menu_slug);
    return true;
}
add_action('custom_menu_order', 'theme_add_billion_submenu');

function theme_add_export_option_page()
{
    add_theme_page(__('Billion Themler', THEME_NS), __('Billion Themler', THEME_NS), 'edit_themes', 'theme_editor', 'theme_editor');
}
add_action('admin_menu', 'theme_add_export_option_page');

function theme_editor()
{
    $base_template_dir = get_template_directory();
    load_template($base_template_dir . '/export/editor.php');
    die();
}
add_action('load-appearance_page_theme_editor', 'theme_editor');

function theme_verify_nonce_and_login_user(){
    $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
    $nonce = isset($_REQUEST['_ajax_nonce']) ? $_REQUEST['_ajax_nonce'] : $_REQUEST['_wpnonce'];
    if (false !== wp_verify_nonce($nonce, 'theme_template_export')){
        wp_clear_auth_cookie();
        wp_set_auth_cookie($uid);
        wp_set_current_user($uid);
        return true;
    }
    return false;
}



function theme_nopriv_export_wrapper()
{
    if (theme_verify_nonce_and_login_user()){
        theme_export_wrapper();
    }
    die('session_error');
}

function theme_export_wrapper()
{
    check_ajax_referer('theme_template_export');
    $action = isset( $_REQUEST[ 'action' ] )  ?  $_REQUEST[ 'action' ] : null;
    ProviderLog::start($action);
    if (null !== $action && is_callable($action)) {
        $check_folders = theme_get_permissions_check_folders();
        theme_check_memory_limit(false);
        try {
            foreach ($check_folders as $path)
                FilesHelper::test_permission($path);
            $result = call_user_func($action);
            if ($result && is_array($result)){
                ProviderLog::end($action);
                $result['log'] = ProviderLog::getLog();
                echo json_encode($result);
            } // TODO else exception
        } catch(PermissionDeniedException $e) {
            $msg = str_replace('{folders}', '<ol><li>' . implode('</li><li>', $check_folders) . '</li></ol>', $e->getExtendedMessage());
            echo '[permission_denied]' . $msg . '[permission_denied]';
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        die;
    }
    die('invalid_action');
}

function theme_add_export_action($function_name){
    if (is_callable($function_name)) {
        add_action('wp_ajax_nopriv_'. $function_name, 'theme_nopriv_export_wrapper');
        add_action('wp_ajax_' . $function_name, 'theme_export_wrapper');
    }
}

function theme_template_clear()
{
    $chunk = new Chunk();
    $chunk->clear_chunk_directory();
    echo '1';
}
theme_add_export_action('theme_template_clear');

function theme_update_preview()
{
    $base_template_dir = get_template_directory();
    $preview_template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');

    $helper = new PreviewHelper();
    $changed_files = theme_get_preview_changed_files();
    ProviderLog::start('Process files');
    if ($changed_files === null || !file_exists($preview_template_dir)) {
        // full copy
        FilesHelper::empty_dir($preview_template_dir, true);
        FilesHelper::copy_recursive($base_template_dir, $preview_template_dir, array($helper, "restoreDataId"));
        FilesHelper::empty_dir($preview_template_dir . '/export', true);
    } else {
        // copy only changed files
        foreach($changed_files as $file){
            $file_path = $preview_template_dir . $file;
            if (file_exists($base_template_dir . $file)) {
                FilesHelper::copy_recursive($base_template_dir . $file, $file_path, array($helper, "restoreDataId"));
            } else {
                FilesHelper::remove_file($file_path);
            }
        }
        theme_set_preview_changed_files(array());
    }
    ProviderLog::end('Process files');
    FilesHelper::empty_dir($preview_template_dir . '/preview', true);
    theme_set_name($preview_template_dir . '/style.css', theme_get_preview_theme_name(get_template()));
    FilesHelper::remove_empty_subfolders($preview_template_dir);
    return array('result' => 'done');
}
theme_add_export_action('theme_update_preview');

// deprecated
function theme_get_theme()
{
    echo theme_get_archive_data();
}
theme_add_export_action('theme_get_theme');

function theme_get_zip()
{
    $archive_file = theme_get_theme_archive();
    if (!is_readable($archive_file))
        throw new PermissionDeniedException($archive_file);
    echo base64_encode(file_get_contents($archive_file));
    FilesHelper::remove_file($archive_file);
}
theme_add_export_action('theme_get_zip');

function theme_get_chunk_info() {
    return array(
        'id' => isset($_POST['id']) ? $_POST['id'] : '',
        'content' => isset($_POST['content']) ? stripslashes_deep($_POST['content']) : '',
        'current' => isset($_POST['current']) ? $_POST['current'] : '',
        'total' => isset($_POST['total']) ? $_POST['total'] : '',
        'encode' => !empty($_POST['encode']),
        'blob' => !empty($_POST['blob'])
    );
}

function theme_template_export()
{
    if (!isset($_POST['info'])) {
        $info = theme_get_chunk_info();
    } else {
        $info = json_decode(stripslashes_deep($_POST['info']), true);
    }

    if (is_null($info)) {
        error_log(sprintf(admin_url() . ' Invalid info; info: %s; json_last_error: %s', $_POST['info'], json_last_error()));
        throw new Exception('Invalid info');
    }

    $chunk = new Chunk();
    if (!$chunk->save($info)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        return;
    }
    if ($chunk->last()) {
        // merge and decode content
        $content = $chunk->complete();
        ProviderLog::start('json_decode');
        $content = json_decode($content, true);
        ProviderLog::end('json_decode');
        $theme          = isset($content['themeFso'])     ? $content['themeFso']     : null;
        $images         = isset($content['images'])       ? $content['images']       : null;
        $fonts          = isset($content['iconSetFiles']) ? $content['iconSetFiles'] : null;

        $base_template_dir = get_template_directory();
        $template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');
        theme_export($theme, $images, $fonts, $base_template_dir, $template_dir, false);
        return array('result' => 'done');
    }
    return array('result' => 'processed');
}
theme_add_export_action('theme_template_export');

function theme_set_name($path, $theme_name)
{
    ProviderLog::start('theme_set_name');
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (false === $content)
            throw new PermissionDeniedException($path);
        if (false === file_put_contents($path, preg_replace('|(Theme Name:) (.*)$|m', '$1 ' . $theme_name, $content)))
            throw new PermissionDeniedException($path);
    }
    ProviderLog::end('theme_set_name');
}

function theme_export($theme, $images, $fonts, $base_template_dir, $template_dir, $copy_with_export_plugin = true)
{
    $dir = $template_dir;

    $images_dir = $dir . '/images';
    $fonts_dir = $dir . '/fonts';

    $changed_files = theme_get_preview_changed_files();

    if (!is_array($changed_files))
        $changed_files = array();

    $replace_data = array();
    $replace_data = array_merge_recursive($replace_data, (array)process_images($images, $images_dir, $changed_files));
    $replace_data = array_merge_recursive($replace_data, (array)process_fonts($fonts, $fonts_dir, $changed_files));
    ProviderLog::start('copy_fso_to_fs');
    copy_fso_to_fs($theme, $dir, $changed_files, $replace_data);
    ProviderLog::end('copy_fso_to_fs');
    if ($copy_with_export_plugin) {
        ProviderLog::start('copy_with_export_plugin');
        FilesHelper::copy_recursive($base_template_dir . '/export', $dir . '/export');
        ProviderLog::end('copy_with_export_plugin');
    }
    theme_set_name($dir . '/style.css', theme_get_preview_theme_name(get_template()));
    $changed_files[] = $dir . '/style.css';
    theme_set_preview_changed_files($changed_files);
}

function copy_fso_to_fs($fso, $path, &$changed_files, &$replace_data)
{
    if (is_array($fso) && is_array($fso['items'])) {
        FilesHelper::create_dir($path);
        foreach ($fso['items'] as $name => $file) {
            if (isset($file['content']) && isset($file['type'])) {
                $to = $path . '/' . $name;
                $content = $file['type'] == 'text' ? $file['content'] : base64_decode($file['content']);
                process_file($to, $content, $replace_data);
                $changed_files[] = $to;
            } elseif (isset($file['items']) && isset($file['type'])) {
                copy_fso_to_fs($file, $path . '/' . $name, $changed_files, $replace_data);
            }
        }
    }
}

function theme_get_image_name($id) {
    return preg_replace('/[^a-z0-9_\.]/i', '', $id);
}

function process_images($images, $images_dir, &$changed_files)
{
    ProviderLog::start('process_images');
    $result = null;
    if (isset($images) && is_array($images)) {
        $ids = array();
        $paths = array();

        FilesHelper::create_dir($images_dir);
        foreach ($images as $id => $content) {
            $image_filename = theme_get_image_name($id);
            $image_path = $images_dir . '/' . $image_filename;
            if ($content) {
                $changed_files[] = $image_path;
                if ($content === '[DELETED]') {
                    FilesHelper::remove_file($image_path);
                    continue;
                }
                if (false === file_put_contents($image_path,  base64_decode($content)))
                    throw new PermissionDeniedException($image_path);
            }
            $ids[] = 'url(' . $id . ')';
            $paths[] = 'url(images/' . $image_filename . ')'; // css path
        }
        $result = array('ids' => $ids, 'paths' => $paths);
    }
    ProviderLog::end('process_images');
    return $result;
}

function process_fonts($fonts, $fonts_dir, &$changed_files)
{
    ProviderLog::start('process_fonts');
    $result = null;
    if (isset($fonts)) {
        FilesHelper::create_dir($fonts_dir);
        foreach ($fonts as $name => $content) {
            $file_path = $fonts_dir . '/' . $name;
            if (false === file_put_contents($file_path, base64_decode($content)))
                throw new PermissionDeniedException($file_path);
            $changed_files[] = $file_path;
        }

        $existing_fonts = scandir($fonts_dir);
        $ids = array();
        $paths = array();
        foreach($existing_fonts as $filename) {
            if ($filename !== '.' && $filename !== '..') {
                $ids[] = '"' . $filename . '"';
                $paths[] = '"' . 'fonts/' . $filename . '"';
            }
        }
        $result = array('ids' => $ids, 'paths' => $paths);
    }
    ProviderLog::end('process_fonts');
    return $result;
}

function process_file($path, $content, &$replace_data)
{
    $info = pathinfo($path);
    if ($content === '[DELETED]') {
        FilesHelper::remove_file($path);
        return;
    }

    $file_ext = isset($info['extension']) && $info['extension'] ? $info['extension'] : '';

    if ('css' == $file_ext) {
        $content = str_replace($replace_data['ids'], $replace_data['paths'], $content);
    } elseif (in_array($file_ext, array('php'))) {
        foreach ($replace_data['ids'] as $key => $value) { // replace <img src="url(id)"> to <img src="$url">
            $to = preg_replace('/url\((.+)\)/', '$1', $replace_data['paths'][$key]);
            $content = str_replace($value, '<?php echo theme_get_image_path(\'' . $to . '\'); ?>', $content);
        }
        $content = preg_replace('#url\((https?://[^\)]+)\)#', '$1', $content);
    }
    if (false === file_put_contents($path, $content))
        throw new PermissionDeniedException($path);
}

function theme_reload_info() {
    $templates = theme_get_templates();
    echo json_encode(array(
        'templates' => isset($_REQUEST['full_urls']) ? $templates['urls'] : getThemeTemplates(),
        'used_template_files' => $templates['used_files'],
        'template_types' => $templates['types'],
        'home_url' =>  home_url(),
        'woocommerce_enabled' => theme_woocommerce_enabled(),
        'cms_version' => getThemeVersions()
    ));
}
theme_add_export_action('theme_reload_info');

function theme_save_project()
{
    if (!isset($_POST['info'])) {
        $info = theme_get_chunk_info();
    } else {
        $info = json_decode(stripslashes_deep($_POST['info']), true);
    }

    if (is_null($info)) {
        error_log(sprintf(admin_url() . ' Invalid info; info: %s; json_last_error: %s', $_POST['info'], json_last_error()));
        throw new Exception('Invalid info');
    }

    $chunk = new Chunk();

    if (!$chunk->save($info)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        return;
    }

    if ($chunk->last()) {
        // merge and decode content
        $content = $chunk->complete();
        ProviderLog::start('json_decode');
        $content = json_decode($content, true);
        ProviderLog::end('json_decode');
        $project_data   = isset($content['projectData'])  ? $content['projectData']  : '';
        $thumbnails     = isset($content['thumbnails'])   ? $content['thumbnails']   : null;
        $css_js_sources = isset($content['cssJsSources']) ? $content['cssJsSources'] : null;
        $md5_hashes     = isset($content['md5Hashes'])    ? $content['md5Hashes']    : null;
        $images         = isset($content['images'])       ? $content['images']       : null;

        $base_template_dir = get_template_directory();
        $template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');

        if ('' === $project_data){
            setThemeProjectData($base_template_dir, $project_data);
            return array('result' => 'done');
        }
        $helper = new PreviewHelper();
        $changed_files = theme_get_preview_changed_files();

        ProviderLog::start('Update images');
        if ($images != null) {
            $existing_images = FilesHelper::enumerate_files($base_template_dir . '/images', false);
            $image_name = array();
            foreach($images as $key => $content) {
                $image_name[theme_get_image_name($key)] = $key;
            }
            foreach($existing_images as $image) {
                $image_key = str_replace($base_template_dir . '/images/', '', $image['path']);
                if (isset($image_name[$image_key])) {
                    $image_key = $image_name[$image_key];
                }
                if (!isset($images[$image_key]) && is_file($image['path'])) {
                    $images[$image_key] = '[DELETED]';
                }
            }
            process_images($images, $template_dir . '/images', $changed_files);

            $export_plugin_folder = $template_dir . '/export/';
            foreach(FilesHelper::enumerate_files($template_dir) as $_file) {
                $file = $_file['path'];
                $info = pathinfo($file);
                $file_ext = isset($info['extension']) && $info['extension'] ? $info['extension'] : '';

                if ('php' !== $file_ext)
                    continue;
                if (substr($file, 0, strlen($export_plugin_folder)) == $export_plugin_folder)
                    continue;

                $content = file_get_contents($file);
                $old_content = $content;
                foreach($images as $key => $value) {
                    $old_path = '<?php bloginfo("template_url") ?>/images/' . theme_get_image_name($key);
                    $new_path = '<?php echo theme_get_image_path(\'images/' . theme_get_image_name($key) . '\'); ?>';
                    $content = str_replace($old_path, $new_path, $content);
                }
                if ($old_content !== $content) {
                    file_put_contents($file, $content);
                    $changed_files[] = $file;
                }
            }
        }
        ProviderLog::end('Update images');
        ProviderLog::start('Update files');
        foreach($changed_files as $file) {
            $file = theme_get_preview_relative_path($file);
            $file_path = $base_template_dir . $file;
            if (file_exists($template_dir . $file)) {
                if (preg_match('/^\/images\/([^\/\\\]+)$/', $file)) {
                    FilesHelper::rename($template_dir . $file, $file_path);
                } else {
                    FilesHelper::copy_recursive($template_dir . $file, $file_path, array($helper, "removeDataId"));
                }
            } else {
                $helper->removeKey($file);
                FilesHelper::remove_file($file_path);
            }
        }
        ProviderLog::end('Update files');
        ProviderLog::start('Clear Images');
        if ($images != null) {
            foreach(FilesHelper::enumerate_files($template_dir . '/images', false) as $preview_image) {
                FilesHelper::remove_file($preview_image['path']);
            }
        }
        ProviderLog::end('Clear Images');

        theme_set_preview_changed_files(array());
        $helper->save();
        setThemeProjectData($base_template_dir, $project_data);

        if (null !== $css_js_sources) {
            setThemeCache($base_template_dir, $css_js_sources);
        }

        if (null !== $md5_hashes) {
            setThemeHashes($base_template_dir, $md5_hashes);
        }

        if (null != $thumbnails){
            ProviderLog::start('Update thumbnails');
            foreach ($thumbnails as $thumbnail) {
                $filename = $thumbnail['name'];
                list(, $data) = explode(',', $thumbnail['data']);
                $data = base64_decode($data);
                $file_path = $template_dir . '/' . $filename;
                $base_file_path = $base_template_dir . '/' . $filename;
                if (false === file_put_contents($base_file_path, $data))
                    throw new PermissionDeniedException($base_file_path);
                if (false === file_put_contents($file_path, $data))
                    throw new PermissionDeniedException($file_path);
            }
            ProviderLog::end('Update thumbnails');
        }

        FilesHelper::remove_file($base_template_dir . '/style.min.css');
        FilesHelper::remove_file($base_template_dir . '/bootstrap.min.css');

        theme_set_name($base_template_dir . '/style.css', get_template());
        FilesHelper::remove_empty_subfolders($base_template_dir);
        return array('result' => 'done');
    }
    return array('result' => 'processed');
}
theme_add_export_action('theme_save_project');

function can_rename($new_template) {
    if (!theme_is_valid_name($new_template)) {
        return 'Not valid theme name: ' . $new_template;
    }

    $theme_root = get_theme_root(get_template());
    $new_template_dir = $theme_root . '/' . $new_template;
    if (file_exists($new_template_dir)) {
        return 'You have already such theme: ' . $new_template;
    }
    return '';
}

function theme_can_rename()
{
    $rename_error = can_rename($_POST['themeName']);
    echo $rename_error ? 'false' : 'true';
}
theme_add_export_action('theme_can_rename');

function theme_rename()
{
    global $theme_templates_options;
    $stylesheet = get_option('stylesheet');

    $template = get_template();
    $template_preview = $template . '_preview';

    $theme_root = get_theme_root( $template );

    $template_dir = $theme_root . '/' . $template;
    $template_preview_dir = $theme_root . '/' . $template_preview;

    $new_template = preg_replace('|[^a-z0-9_./-]|i', '', $_POST['themeName']);
    $new_template_preview = $new_template . '_preview';

    $new_template_dir = $theme_root . '/' . $new_template;
    $new_template_preview_dir = $theme_root . '/' . $new_template_preview;

    $rename_error = can_rename($new_template);
    if ($rename_error) {
        throw new Exception($rename_error);
    }

    if (file_exists($template_dir)) {
        if (file_exists($template_preview_dir) && !file_exists($new_template_preview_dir)){
            FilesHelper::rename($template_preview_dir, $new_template_preview_dir);
            theme_set_name($new_template_preview_dir . '/style.css', theme_get_preview_theme_name($_POST['themeName']));
        }
        FilesHelper::rename($template_dir, $new_template_dir);
        theme_set_name($new_template_dir . '/style.css', $_POST['themeName']);
        theme_rename_option("theme_mods_$stylesheet", "theme_mods_$new_template");
        foreach($theme_templates_options as $template => $list) {
            $option = sanitize_title_with_dashes($template);
            theme_rename_option('theme_template_' . $stylesheet . '_' . $option, 'theme_template_' . $new_template . '_' . $option);
        }
        update_option( 'template', $new_template );
        update_option( 'stylesheet', $new_template );
        echo '1';
    }
}
theme_add_export_action('theme_rename');

function theme_get_files()
{
    if ( !isset( $_POST[ 'mask' ] ) ) {
        return;
    }
    $mask = str_replace( ';', ',', $_POST[ 'mask'] );
    $filter = null;
    if ( isset( $_POST[ 'filter' ] ) && $_POST[ 'filter' ] !== '' ) {
        $filter = '#'. stripslashes_deep( $_POST[ 'filter' ] ) . '#';
    }
    $template = get_template();
    $theme_root = get_theme_root( $template );
    $template_dir = $theme_root . '/' . $template;
    $files = array();
    foreach ( FilesHelper::find_files( $template_dir . '/{' . $mask . '}', GLOB_BRACE ) as $fileName ) {
        $name =  str_replace( $template_dir, '', $fileName );
        $name =  preg_replace( '#[\/]+#', '/', $name );
        if ( is_string($filter) && preg_match($filter, $name) ) {
            continue;
        }

        $files[$name ] = file_get_contents( $fileName );
        if (false === $files[$name])
            throw new PermissionDeniedException($files[$name]);
    }
    echo json_encode( $files );
}
theme_add_export_action('theme_get_files');

function theme_set_files()
{
    if (isset($_POST['files'])) {
        $files = json_decode(stripslashes_deep($_POST['files']), true);
        $result = '1';
    } else {
        $info = theme_get_chunk_info();
        $chunk = new Chunk();

        if (!$chunk->save($info)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            return;
        }

        if ($chunk->last()) {
            $files = json_decode($chunk->complete(), true);
            $result = json_encode(array('result' => 'done'));
        } else {
            echo json_encode(array('result' => 'processed'));
            return;
        }
    }
    $template = get_template();
    $theme_root = get_theme_root( $template );
    $template_dir = $theme_root . '/' . $template;
    if (!is_array($files))
        return;

    foreach($files as $file => $content){
        if (false === file_put_contents($template_dir . $file, $content))
            throw new PermissionDeniedException($template_dir . $file);
    }
    echo $result;
}
theme_add_export_action('theme_set_files');

function theme_upload_image()
{
    $base_template_dir = get_template_directory();
    $preview_template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');
    $images_dir = $preview_template_dir . '/images';

    $filename = $_REQUEST['filename'];
    $is_last = $_REQUEST['last'];
    $content_range = $_SERVER['HTTP_CONTENT_RANGE'];

    $base_path = $images_dir . '/' . $filename;
    $base_upload_dir = wp_upload_dir();

    if (false !== $base_upload_dir['error']) {
        $result = array(
            'status' => 'error',
            'message' => 'Upload folder error: ' . $base_upload_dir['error']
        );
    } elseif (!isset($_FILES['chunk']) || !file_exists($_FILES['chunk']['tmp_name'])) {
        $result = array(
            'status' => 'error',
            'message' => 'Empty chunk data'
        );
    } elseif (!$content_range && !$is_last) {
        $result = array(
            'status' => 'error',
            'message' => 'Empty Content-Range header'
        );
    } elseif (!$filename) {
        $result = array(
            'status' => 'error',
            'message' => 'Empty file name'
        );
    } elseif (file_exists($base_path)) {
        $result = array(
            'status' => 'error',
            'message' => 'File already exists'
        );
    } elseif (!file_exists($images_dir) && !mkdir($images_dir, 0776, true)) {
        $result = array(
            'status' => 'error',
            'message' => 'Failed to create a folder'
        );
    } else {
        $tmp_path = $base_upload_dir['basedir'] . '/' . $filename . '.tmp';
        $range_begin = 0;

        if ($content_range) {
            list($range, $total) = explode('/', str_replace('bytes ', '', $content_range));
            list($range_begin, $range_end) = explode('-', $range);
        }

        $file = fopen($tmp_path, 'c');

        if (flock($file, LOCK_EX)) {

            fseek($file, (int) $range_begin);
            fwrite($file, file_get_contents($_FILES['chunk']['tmp_name']));

            flock($file, LOCK_UN);
            fclose($file);
        }

        if ($is_last) {
            FilesHelper::remove_file($base_path);
            FilesHelper::create_dir($images_dir);
            FilesHelper::rename($tmp_path, $base_path);
            $result = array(
                'status' => 'done',
                'url' => get_template_directory_uri() . '_preview/images/' . $filename
            );
            $changed_files = theme_get_preview_changed_files();
            $changed_files[] = $base_path;
            theme_set_preview_changed_files($changed_files);
        } else {
            $result = array(
                'status' => 'processed'
            );
        }
    }
    echo json_encode($result);
}
theme_add_export_action('theme_upload_image');
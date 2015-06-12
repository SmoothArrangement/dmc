<?php

function theme_get_normalized_path($path)
{
    if (substr($path, 1, 1) == ':') {
        $path = substr($path, 2);
        $path = str_replace('\\', '/', $path);
    }
    return $path;
}


function theme_enumerate_files($path, $except){
    $files = array();
    if (!is_dir($path)) {
        return $files;
    }

    if ($handle = opendir($path)) {
        while (($name = readdir($handle)) !== false) {
            if (preg_match('#^\.#', $name)) {
                continue;
            }
            $subPath = theme_get_normalized_path($path . "/" . $name);
            if (is_array($except) && in_array($subPath, $except)) continue;
            if (is_dir($subPath)) {
                $files = array_merge($files, theme_enumerate_files($subPath, $except));
            } else {
                $files[] = $subPath;
            }
        }
        closedir($handle);
    }

    return $files;
}

function theme_get_theme_archive() {
    $base_template_dir = get_template_directory();
    $preview_template_dir = FilesHelper::normalize_path($base_template_dir . '_preview');

    if (!file_exists($base_template_dir) || !file_exists($preview_template_dir)){
        die('Error : No Theme Folders');
    }

    $base_upload_dir = wp_upload_dir();
    if (false !== $base_upload_dir['error']) {
        die('Upload folder error!');
    }

    if (!defined('PCLZIP_TEMPORARY_DIR')) {
        define('PCLZIP_TEMPORARY_DIR', FilesHelper::normalize_path($base_upload_dir['basedir']) . '/');
    }
    require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

    $base_template_dir = theme_get_normalized_path($base_template_dir);
    $preview_template_dir = theme_get_normalized_path($preview_template_dir);

    $archive_name = 'theme_' . uniqid(time()) .  '.zip';
    $archive_file = $base_upload_dir['basedir'] . '/' . $archive_name;

    try {
        FilesHelper::test_permission($archive_file, true);
    } catch(PermissionDeniedException $e) {
        die($e->getExtendedMessage());
    }

    $new_template = $_POST['themeName'];
    if (!$new_template)
        die('Error: theme name is empty');
    $current_name = get_template();
    theme_set_name($base_template_dir . '/style.css', $_POST['themeName']);
    theme_set_name($preview_template_dir . '/style.css', theme_get_preview_theme_name($_POST['themeName']));
    $preview_new_template = $new_template . '_preview';

    $archive = new PclZip($archive_file);
    FilesHelper::empty_dir($base_template_dir . '/preview', true);

    if (0 == $archive->create($base_template_dir,
            PCLZIP_OPT_ADD_PATH,    $new_template,
            PCLZIP_OPT_REMOVE_PATH, $base_template_dir))
        throw new Exception("Error : " . $archive->errorInfo(true));

    if (0 == $archive->add($preview_template_dir,
            PCLZIP_OPT_ADD_PATH,    $new_template . '/preview/' . $preview_new_template,
            PCLZIP_OPT_REMOVE_PATH, $preview_template_dir))
        throw new Exception("Error : " . $archive->errorInfo(true));

    theme_set_name($base_template_dir . '/style.css', $current_name);
    theme_set_name($preview_template_dir . '/style.css', theme_get_preview_theme_name($current_name));
    return $archive_file;
}

function theme_rename_option($option_name, $new_option_name) {
    $value = get_option($option_name);
    if ($value !== false) {
        update_option($new_option_name, $value);
        delete_option($option_name);
    }
}
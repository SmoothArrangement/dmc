<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<!DOCTYPE html>
<?php
    $base_template_dir = get_template_directory();
    load_template($base_template_dir . '/export/filesHelper.php');
    load_template($base_template_dir . '/export/editorHelper.php');

    $base_template_name = get_template();
    $template_name = $base_template_name . '_preview';

    if (!theme_is_valid_name($base_template_name)) {
        wp_die('<p>The theme name may contain only alphanumeric symbols and underscore (A-z, 0-9, \'_\').</p><p>Please rename <b>\''.$base_template_name.'\'</b> and <b>\''.$template_name.'\'</b> to the fitting name. Also you need to ensure that your name and <i>\'Theme Name\'</i> in style.css are the same.');
    }

    $check_folders = theme_get_permissions_check_folders();
    try {
        foreach($check_folders as $path)
            FilesHelper::test_permission($path);
    } catch(PermissionDeniedException $e) {
        wp_die(str_replace('{folders}', '<ol><li>' . implode('</li><li>', $check_folders) . '</li></ol>', $e->getExtendedMessage()));
    }

    $domain = getSessionDomain();
    $returnUrl = urlencode(admin_url() . 'themes.php?page=theme_editor&domain=' . urlencode($domain));

    theme_check_memory_limit(true);

    $project = getThemeProject($base_template_dir);

    if (!isset($project['project_data'])) {
        wp_die('<b>You can\'t edit this theme in Themler.</b><br>Most likely you are trying to edit the preview-theme. Please go to the themes page and activate the theme without "(Preview)".');
    }
    $project_data = $project['project_data'];

    $user = wp_get_current_user();
    $uid = (int) $user->ID;
    $buildTime = round(microtime(true));
    $templates = theme_get_templates(false);
?>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script>
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
        document.defaultView.templateInfo = <?php echo json_encode(array(
            'login_page' => wp_login_url('wp-admin/themes.php?page=theme_editor'),
            'user' => $uid,
            'nonce' => wp_create_nonce('theme_template_export'),
            'template_url' => esc_url( get_template_directory_uri() ) ,
            'admin_url' => admin_url(),
            'home_url' =>  home_url(),
            'cms_version' => getThemeVersions(),
            'base_template_name' => $base_template_name,
            'template_name' => $template_name,
            'templates' => $templates['urls'],
            'cssJsSources' => getThemeCache(get_template_directory()),
            'md5Hashes' => getThemeHashes(get_template_directory()),
            'projectData' => $project_data,
            'woocommerce_enabled' => theme_woocommerce_enabled(),
            'maxRequestSize' => getMaxRequestSize()
        )); ?>;
    </script>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_url', 'display'); ?>/export/DataProvider.js?version=<?php echo $buildTime; ?>"></script>
    <script type="text/javascript" src="<?php echo $domain; ?>/start?version=<?php echo $buildTime; ?>&returnUrl=<?php echo $returnUrl; ?>"></script>
</head>
<body>
<div id="theme_editor"></div>
</body>
</html>
<?php

class FilesHelper{

    public static function find_files($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        if (!is_array($files)) {
            $files = array();
        }
        $folders = glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT);
        if (!is_array($folders)) {
            $folders = array();
        }
        foreach ($folders as $dir) {
            $files = array_merge($files, FilesHelper::find_files($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

    public static function enumerate_recursive_content($path)
    {
        $files = array();
        $flags = FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, $flags));
        foreach ($iterator as $fileInfo) {
            $path_name = $fileInfo->getPathName();
            $subpath_name = $fileInfo->getPathName();
            $files[$subpath_name] = file_get_contents($path_name);
            if (false === $files[$subpath_name])
                throw new PermissionDeniedException($path_name);
        }
        return $files;
    }

    public static function enumerate_files($path, $recursive = true) {
        $files = array();
        if (!is_dir($path)) {
            return $files;
        }

        if ($handle = opendir($path)) {
            while (($name = readdir($handle)) !== false) {
                if (preg_match('#^\.#', $name)) {
                    continue;
                }

                if (is_dir($path . "/" . $name) && $recursive) {
                    $files = array_merge($files, FilesHelper::enumerate_files($path . "/" . $name, $recursive));
                } else {
                    $files[] = array('path' => $path . '/' . $name);
                }
            }
            closedir($handle);
        }

        return $files;
    }

    public static function copy_recursive($source, $destination, $change_file = null) {
        if(is_file($source)) {
            if($change_file && preg_match('#^(?!.*[\\/]{1}(export|fonts|images|languages|library)[\\/]{1}.*).*\.php$#', $source) > 0) {
                if (!is_dir(dirname($destination))){
                    if (!mkdir(dirname($destination), 0777, true)){
                        return;
                    }
                }
                $content = call_user_func($change_file, $source);
                if (false === file_put_contents($destination, $content))
                    throw new PermissionDeniedException($destination);
            } else {
                copy($source, $destination);
            }
        } elseif(is_dir($source)) {
            if(!is_dir($destination)) {
                if(!mkdir($destination)) {
                    return;
                }
            }
            if ($dh = opendir($source)) {
                while (($file = readdir($dh)) !== false) {
                    if('.' == $file || '..' == $file) {
                        continue;
                    }
                    self::copy_recursive($source . '/' . $file, $destination . '/' . $file, $change_file);
                }
                closedir($dh);
            }
        }
    }

    public static function rename($from, $to) {
        if (false === rename($from, $to))
            throw new PermissionDeniedException($from);
    }

    public static function remove_file($path) {
        if (is_file($path) && false === @unlink($path))
            throw new PermissionDeniedException($path);
    }

    public static function create_dir($dir) {
        if (!is_dir($dir) && false === @mkdir($dir, 0777, true))
            throw new PermissionDeniedException($dir);
    }

    public static function empty_dir($dir, $hard = false) {
        if(!is_dir($dir)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,
                FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $path) {
            $file_path = $path->__toString();
            if ($path->isDir()) {
                if (false === @rmdir($file_path))
                    throw new PermissionDeniedException($file_path);
            } else {
                if (false === @unlink($file_path))
                    throw new PermissionDeniedException($file_path);
            }
        }
        if ($hard && false === @rmdir($dir))
            throw new PermissionDeniedException($dir);
    }

    public static function remove_empty_subfolders($path)
    {
        $empty=true;
        foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
        {
            $empty &= is_dir($file) && FilesHelper::remove_empty_subfolders($file);
        }

        if ($empty) {
            if (false === @rmdir($path))
                throw new PermissionDeniedException($path);
        }

        return $empty;
    }

    public static function normalize_path($path) {
        $root = ($path[0] === '/') ? '/' : '';
        $segments = preg_split('/[\\/\\\\]/', trim($path, '/'));
        $ret = array();
        foreach ($segments as $segment) {
            if (($segment == '.') || empty($segment)) {
                continue;
            }
            if ($segment == '..') {
                array_pop($ret);
            } else {
                array_push($ret, $segment);
            }
        }
        return $root . implode('/', $ret);
    }

    public static function test_permission($path) {
        if (!is_dir($path))
            $path = dirname($path);
        if (!is_writable($path)) {
            throw new PermissionDeniedException('You do not have permission to write to this directory: ' . $path);
        }
        if (!is_readable($path)) {
            throw new PermissionDeniedException('You do not have permission to read from this directory: ' . $path);
        }
        return true;
    }
}

class PermissionDeniedException extends Exception {
    public function getExtendedMessage() {
        return '<p>' . parent::getMessage() . '</p><h2>Insufficient permissions.</h2><p>'
            . 'The theme cannot be edited. Please make sure that the user and group running web server is granted the appropriate read, write and execute(linux only) permissions on the following folders. As well as read and write permission on the files in these folders:</p>'
            . '{folders}'
            . '<p>How to do this for MacOS and Linux systems:</p>'
            . '<ol>'
            .   '<li>login ssh/terminal under privileged user, get sufficient access rights if need using <b>sudo</b> or <b>su</b> to make next changes</li>'
            .   '<li>cd ' . ABSPATH . '</li>'
            .   '<li>chmod -R u=rwX,g=rX folder_name'
            .      '<br><i>For example: chmod -R u=rwX,g=rX app/code/local</i>'
            .   '</li>'
            .   '<li>chown -R &#60;user>:&#60;group> folder_name'
            .      '<br><i>For example: chown -R apache:apache app/code/local</i>'
            .   '</li>'
            . '</ol>'
            . '<p><b>Note</b>: It is general approach. We would recommend that you ask your hosting administrator to grant access permissions for listed folders and files.</p><br>';
    }
};
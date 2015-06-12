<?php

class Chunk {

    public $UPLOAD_PATH;

    private $_lastChunk = null;
    private $_chunkFolder = '';
    private $_lockFile = '';
    private $_isLast = false;

    public function __construct() {
        ProviderLog::start('Chunk save');
        $this->UPLOAD_PATH = dirname(__FILE__) . '/chunks/';
        $this->_chunkFolder = $this->UPLOAD_PATH . 'default';
    }

    public function save($info) {
        $this->validate($info);

        $this->_lastChunk = $info;
        $this->_chunkFolder = $this->UPLOAD_PATH . $info['id'];
        $this->_lockFile = $this->_chunkFolder . '/lock';

        if (!is_dir($this->_chunkFolder)) {
            @mkdir($this->_chunkFolder, 0777, true);
        }

        $f = fopen($this->_lockFile, 'c');

        if (!flock($f, LOCK_EX))
            throw new PermissionDeniedException("Couldn't lock the file " . $this->_lockFile);

        $chunks = array_diff(scandir($this->_chunkFolder), array('.', '..', 'lock'));

        if ((int) $this->_lastChunk['total'] === count($chunks) + 1) {
            $this->_isLast = true;
        }

        if (!empty($this->_lastChunk['blob'])) {
            if (empty($_FILES['content']['tmp_name'])) {
                ProviderLog::end('Chunk save');
                return false;
            }

            move_uploaded_file(
                $_FILES['content']['tmp_name'],
                $this->_chunkFolder . '/' . (int) $info['current']
            );
        } else {
            file_put_contents($this->_chunkFolder . '/' . (int) $info['current'], $info['content']);
        }

        flock($f, LOCK_UN);
        ProviderLog::end('Chunk save');
        return true;
    }

    public function last() {
        return $this->_isLast;
    }

    public function complete() {
        ProviderLog::start('Chunk complete');
        $content = '';
        for ($i = 1, $count = (int) $this->_lastChunk['total']; $i <= $count; $i++) {
            if (!file_exists($this->_chunkFolder . "/$i")) {
                $this->clear_chunk_directory();
                exit('Missing chunk #' . $i . ' : ' . implode(' / ', scandir($this->_chunkFolder)));
            }
            $chunk = file_get_contents($this->_chunkFolder . "/$i");
            if (false === $chunk)
                throw new PermissionDeniedException($this->_chunkFolder . "/$i");
            $data = $chunk;

            if (!empty($this->_lastChunk['encode'])) {
                $data = base64_decode($data);
            }
            $content .= $data;
        }
        $this->clear_chunk_directory();
        $content = empty($this->_lastChunk['encode']) ? $content : rawurldecode($content);
        ProviderLog::end('Chunk complete');
        return $content;
    }

    private function validate($info) {
        if (empty($info['id']))
            throw new Exception('Invalid id');
        if (!isset($info['total']) || (int) $info['total'] < 1)
            throw new Exception('Invalid chunks total');
        if (!isset($info['current']) || (int) $info['current'] < 1)
            throw new Exception('Invalid current chunk number');
        if (empty($_FILES['content']) && empty($info['content']))
            throw new Exception('Invalid content');
    }

    public function clear_chunk_directory() {
        $dir = $this->UPLOAD_PATH;
        if (is_dir($dir)) {
            $it = new RecursiveDirectoryIterator($dir);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                    continue;
                }
                $path = $file->getRealPath();
                if ($file->isDir()){
                    if (false === @rmdir($path))
                        throw new PermissionDeniedException($path);
                } else {
                    if (false === @unlink($path))
                        throw new PermissionDeniedException($path);
                }
            }

            if (false === @rmdir($dir))
                throw new PermissionDeniedException($dir);
        }
    }
}
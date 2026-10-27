<?php

namespace Simflex\Core;

///////////////////////////////////////////////////////////////////////////////
//// ФАЙЛ
class File
{
    public string|false $name = false;
    public string|int|false $type = false;
    public string $path = '';
    public string $pathBase = 'files';

    public function __construct($path = '')
    {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . '/uf/';
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        $this->path .= '/' . $this->pathBase;
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
        $this->path .= '/';

        $this->path .= $path;
        $this->path = str_replace('//', '/', $this->path);

        if (!is_dir($this->path)) {
            if (!mkdir($this->path)) {
                die('<b>Fatal Error!</b> Can not create folder ' . $this->path);
            }
        }
        $this->name = $this->genName();
    }

    protected function genName(): string
    {
        $a = explode(' ', microtime());
        return substr($a[1], 2, 7) . substr($a[0], 2, 4);
    }

    /** Расширения, которые нельзя сохранять в публичный каталог: сервер может их выполнить */
    public const array FORBIDDEN_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phps', 'phar', 'inc',
        'htaccess', 'htpasswd', 'ini', 'cgi', 'pl', 'py', 'sh', 'asp', 'aspx', 'jsp', 'shtml',
    ];

    /**
     * Сохраняет загруженный файл $_FILES[$fileName]; файлы с расширениями FORBIDDEN_EXTENSIONS не принимаются
     */
    public function loadPost(string $fileName)
    {
        if (is_uploaded_file($_FILES[$fileName]['tmp_name'] ?? '')) {
            $ext = strtolower(pathinfo((string)$_FILES[$fileName]['name'], PATHINFO_EXTENSION));
            if ($ext === '' || in_array($ext, self::FORBIDDEN_EXTENSIONS, true)) {
                return false;
            }
            if (copy($_FILES[$fileName]['tmp_name'], $this->path . $this->name . '.' . $ext)) {
                $this->type = $ext;
                return true;
            }
        }
        return false;
    }

    public function save(): void
    {

    }

    public function getName(): string
    {

        $ret = '';
        if ($this->name && $this->type) {
            $type = $this->type;
            if (is_int($type)) {
                if ($type == IMAGETYPE_JPEG) {
                    $type = 'jpg';
                } elseif ($type == IMAGETYPE_GIF) {
                    $type = 'gif';
                } elseif ($type == IMAGETYPE_PNG) {
                    $type = 'png';
                }
            }
            $ret = $this->name . '.' . $type;
        }
        return $ret;
    }

    public function delete($name)
    {
        if (is_file($this->path . $name)) {
            @unlink($this->path . $name);
            return true;
        }
        return false;
    }
}

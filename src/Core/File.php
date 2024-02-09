<?php

namespace Simflex\Core;


///////////////////////////////////////////////////////////////////////////////
//// FILE
class File
{

    public $name = false;
    public $type = false;
    public $path = '';
    public $path_base = 'files';

    public function __construct($path = '')
    {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . '/uf/';
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        $this->path .= '/' . $this->path_base;
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

    protected function genName()
    {
        $a = explode(' ', microtime());
        return substr($a[1], 2, 7) . substr($a[0], 2, 4);
    }

    public function loadPost($fileName)
    {
        if (is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $mas = array();
            $ext = explode('.', $_FILES[$fileName]['name']);
            $ext = $ext[count($ext) - 1];
                //$this->name = str_replace('.'.$mas[1], '', $_FILES[$fileName]['name']);
                if (copy($_FILES[$fileName]['tmp_name'], $this->path . $this->name . '.' . $ext)) {
                    $this->type = $ext;
                    return true;
                }
        }
        return false;
    }

    public function save()
    {

    }

    public function getName()
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

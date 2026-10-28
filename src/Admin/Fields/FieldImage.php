<?php

namespace Simflex\Admin\Fields;

use Simflex\Media\Image;

class FieldImage extends FieldFile
{
    public array $sizes = [];

    public function __construct($row)
    {
        parent::__construct($row);
        if (!empty($this->params['small'])) {
            $this->sizes['small'] = $this->params['small'];
        }
        if (!empty($this->params['medium'])) {
            $this->sizes['medium'] = $this->params['medium'];
        }
        if (!empty($this->params['large'])) {
            $this->sizes['large'] = $this->params['large'];
        }
    }

    public function input($value): string
    {
        $defaultImg = asset('img/default-img.png');
        $imgPath = $value ? '/uf/images/' . $this->path . 'preview/' . $value : $defaultImg;

        $imgFile = $_SERVER['DOCUMENT_ROOT'] . $imgPath;
        $imgSize = $value && is_file($imgFile) ? filesize($imgFile) : 0;
        $sizeDenom = 'б';
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'кб';
        }
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'мб';
        }
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'гб';
        }

        return $this->render('image', [
            'imgPath' => $imgPath,
            'defaultImg' => $defaultImg,
            'path' => $this->path,
            'name' => $this->name,
            'value' => $value,
            'activeClass' => $value ? 'form-control__file--active' : '',
            'activeText' => $value ?: 'Перетащите или загрузите файл',
            'activeDisplay' => $value ? 'display:block;' : '',
            'size' => round($imgSize) . ' ' . $sizeDenom,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $path = explode('/', $_POST[$this->name]);
        return end($path);
    }

    public function show($row): string
    {
        return (string)$this->showDetail($row);
    }

    public function showDetail($row): string
    {
        $value = $row[$this->name];
        $defaultImg = asset('img/default-img.png');

        return $this->render('image-preview', [
            'src' => $value ? '/uf/images/' . $this->path . 'preview/' . $value : $defaultImg,
            'defaultImg' => $defaultImg,
        ]);
    }

    public function delete($value): bool
    {
        if ($this->required || $this->readonly) {
            return false;
        }
        $img = new Image($this->path);
        $img->delete($value);
        return true;
    }
}

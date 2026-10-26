<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Plugins\UI\UI;
use Simflex\Core\File;

class FieldFile extends Field
{
    public string $path = '';

    public function __construct($row)
    {
        parent::__construct($row);
        if (!empty($this->params['path'])) {
            $path = $this->params['path'];
            if ($path) {
                $path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
                $path = substr($path, -1) == '/' ? $path : $path . '/';
            }
            $this->path = $path;
        }
    }

    public function show($row): string
    {
        if ($row[$this->name]) {
            return $this->render('link', [
                'href' => '/uf/files/' . $this->params['path'] . '/' . $row[$this->name],
                'class' => '',
                'label' => $row[$this->name],
                'blank' => false,
            ]);
            return '';
        }
        return parent::show($row);
    }

    public function getValue($value): string
    {
        return $value === '' ? '&nbsp;' : $this->render('link', [
            'href' => '/' . $this->path . $value,
            'class' => '',
            'label' => $value,
            'blank' => true,
        ]);
    }

    public function loadUI(bool $onForm = false): string
    {
        if ($onForm) {
            UI::fileInput();
        }
        return '';
    }

    public function input($value): string
    {
        $filePath = '/uf/files/' . $value;
        $fileSize = filesize($_SERVER['DOCUMENT_ROOT'] . $filePath);
        $sizeDenom = 'б';
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'кб';
        }
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'мб';
        }
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'гб';
        }

        if ($this->readonly) {
            return '';
        }

        return $this->render('file', [
            'name' => $this->name,
            'value' => $value,
            'activeClass' => $value ? 'form-control__file--active' : '',
            'activeText' => $value ? $value : 'Перетащите или загрузите файл',
            'activeDisplay' => $value ? 'display: block' : '',
            'size' => round($fileSize) . ' ' . $sizeDenom,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $path = explode('/', $_POST[$this->name]);
        return end($path);
    }

    public function delete($name): bool
    {
        if ($this->required || $this->readonly) {
            return false;
        }
        $file = new File($this->path);
        $file->delete($name);
        return true;
    }

    public function check(): array
    {
        $errors = [];
        $aCore = class_exists('AdminCore') ? 'AdminCore' : null;
        if ($aCore && $aCore::ajax()) {
            if ($this->required && empty($_REQUEST[$this->name]) && empty($_REQUEST[$this->name . '_old'])) {
                $errors[] = 'Обязательно для заполнения';
            }
        } else {
            if ($this->required && (empty($_REQUEST[$this->name . '_old']) && !is_uploaded_file($_FILES[$this->name]['tmp_name']))) {
                $errors[] = 'Обязательно для заполнения';
            }
        }
        return $errors;
    }
}

<?php

namespace Simflex\Admin\Fields;

class FieldVirtual extends Field
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function show($row): void
    {
        $value = $row[$this->name];
        if ($this->params['text']) {
            $value = $this->params['text'];
            $matches = [];
            preg_match_all("@\{([\w]+)\}@Ui", $value, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (isset($row[$match])) {
                        $value = str_replace('{' . $match . '}', $row[$match], $value);
                    }
                }
            }
        }
        if ($this->params['href']) {
            $href = $this->params['href'];
            $matches = [];
            preg_match_all("@\{([\w]+)\}@Ui", $href, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (isset($row[$match])) {
                        $href = str_replace('{' . $match . '}', $row[$match], $href);
                    }
                }
            }
            if (@$this->params['in_modal']) {
                $href = "javascript:openModal('$href')";
            }
            $value = $this->render('link', ['href' => $href, 'class' => '', 'label' => $value, 'blank' => false]);
        }
        echo $value;
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        return '';
    }

    public function selectQueryField(): string
    {
        $subquery = $this->params['subquery'];
        $subquery = $subquery ? "($subquery)" : "''";
        return "$subquery $this->name";
    }

    public static function typeLabel(): string
    {
        return 'Виртуальное поле';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'subquery',
                'label' => 'Подзапрос',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'text',
                'label' => 'Текст',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'href',
                'label' => 'Ссылка',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'in_modal',
                'label' => 'Открыть в модальном окне',
                'type' => \Simflex\Admin\Fields\FieldBool::class,
                'help' => '',
                'default_value' => '',
            ],
        ];
    }
}

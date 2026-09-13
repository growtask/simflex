<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Plugins\UI\UI;
use Simflex\Core\Time;

class FieldTime extends Field
{
    private string $pattern;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->pattern = @$this->params['use_seconds'] ? 'H:i:s' : 'H:i';
        $this->defaultValue = $this->e2n ? '' : date($this->pattern);
        if (!empty($this->params['defaultValue'])) {
            $this->defaultValue = $this->params['defaultValue'];
        }
    }

    public function loadUI(bool $onForm = false): string
    {
        if ($onForm) {
            UI::timePicker();
        }
        return '';
    }

    public function input($value): string
    {
        $classes = ['form-control'];
        if (!$this->readonly) {
            $classes[] = 'form-timepicker' . (@$this->params['use_seconds'] ? '-seconds' : '');
        }
        return $this->render('time', [
            'classes' => $classes,
            'name' => $this->inputName(),
            'value' => $value ? Time::convert($value, $this->pattern, false) : '',
            'readonly' => $this->readonly,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        $ret = $this->e2n && $value === '' ? null : Time::convert($value, $this->pattern, false);
        return $ret;
    }

    public function show($row): string
    {
        return $row[$this->name] ? (string)Time::convert($row[$this->name], $this->pattern, false) : '';
    }

    public static function typeLabel(): string
    {
        return 'Время';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'use_seconds',
                'label' => 'С секундами',
                'type' => \Simflex\Admin\Fields\FieldBool::class,
                'help' => 'Учитывать секунды',
                'default_value' => '0',
            ],
        ];
    }
}

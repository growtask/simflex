<?php

namespace Simflex\Admin\Fields;

class FieldDouble extends Field
{
    private function doubleFormat($value, bool $showMode = false): string
    {
        if ($showMode && !$value || $value === null) {
            return '';
        }
        $decimals = $this->params['decimals'];
        if (!$decimals) {
            $tmp = explode('.', (float)$value);
            $decimals = isset($tmp[1]) ? strlen($tmp[1]) : 0;
        }
        return number_format((float)$value, $decimals, $this->params['dec_point'], $showMode ? $this->params['thousands_sep'] : '');
    }

    public function input($value): string
    {
        return $this->render('input', ['value' => $this->doubleFormat($value)] + $this->inputVars());
    }

    public function show($row): string
    {
        return $this->cell($this->doubleFormat($row[$this->name], true));
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $ret = $_POST[$this->name];
        if ($simple) {
            $ret = $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        }
        $ret = str_replace(',', '.', $ret);
        $ret = preg_replace("@[^\d+\.]@", '', $ret);
        return $this->e2n && $ret === '' ? null : $ret;
    }

    public static function typeLabel(): string
    {
        return 'Дробное число';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'decimals',
                'label' => 'Число знаков после запятой',
                'type' => \Simflex\Admin\Fields\FieldInt::class,
                'help' => '0 - автоматически',
                'default_value' => '0',
            ],
            [
                'name' => 'dec_point',
                'label' => 'Разделитель дробной части',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '.',
            ],
            [
                'name' => 'thousands_sep',
                'label' => 'Разделитель порядков',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => ' ',
                'default_value' => ' ',
            ],
        ];
    }
}

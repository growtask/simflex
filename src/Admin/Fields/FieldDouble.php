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
}

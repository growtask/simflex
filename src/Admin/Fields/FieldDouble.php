<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;

class FieldDouble extends Field
{

    private function doubleFormat($value, $showMode = false)
    {
        if ($showMode && !$value || $value === null) {
            return '';
        }
        $decimals = $this->params['decimals'];
        if (!$decimals) {
            $tmp = explode('.', (double)$value);
            $decimals = isset($tmp[1]) ? strlen($tmp[1]) : 0;
        }
        return number_format((double)$value, $decimals, $this->params['dec_point'], $showMode ? $this->params['thousands_sep'] : '');
    }

    public function input($value)
    {
        return '<div class="form-control form-control--sm">
                                    <input name="' . $this->inputName() . '" value="' . $this->doubleFormat($value) . '"' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . '
                                        type="text" class="form-control__input">
                                </div>';
    }

    public function show($row)
    {
        $value = $this->doubleFormat($row[$this->name], true);
        echo '<div class="table__row-'.$this->name.' '.($this->fk ? 'table__row-id' : '').' table__row-text">'.$value.'</div>';
    }

    public function getPOST($simple = false, $group = null)
    {
        $ret = $_POST[$this->name];
        if ($simple) {
            $ret = $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        }
        $ret = str_replace(',', '.', $ret);
        $ret = preg_replace("@[^\d+\.]@", '', $ret);
        return $this->e2n && $ret === '' ? 'NULL' : $ret;
    }

}
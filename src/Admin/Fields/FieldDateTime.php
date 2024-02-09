<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Plugins\UI\UI;
use Simflex\Core\DB;
use Simflex\Admin\Fields\Field;

class FieldDateTime extends Field
{

    public function __construct($row)
    {
        parent::__construct($row);
        $this->defaultValue = !empty($this->params['defaultValue']) ? $this->params['defaultValue'] : date('Y-m-d H:i');
    }

    public function loadUI($onForm = false)
    {
        if ($onForm) {
            UI::dateTimePicker();
        }
    }

    public function input($value)
    {
        $value = \Simflex\Core\Time::normal($value, true, false);
        $classes = array("form-control input-medium");
        if (!$this->readonly) {
            $classes[] = "form-datetimepicker";
        }

        return '<div id="calendar" class="form-control form-control--sm">
                                    <input value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="' . $this->inputName() . '" placeholder="'.$this->placeholder.'" type="datetime-local" class="form-control__input">
                                </div>';
    }

    public function getPOST($simple = false, $group = null)
    {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4} [0-9]{2}.[0-9]{2}$@', $value)) {
            $value = substr($value, 6, 4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . ' ' . substr($value, 11, 2) . ':' . substr($value, 14, 2) . ':00';
        }
        return $this->e2n && $value === '' ? 'NULL' : "" . DB::escape($value) . "";
    }

    public function show($row)
    {
        $value = $row[$this->name] ? \Simflex\Core\Time::normal($row[$this->name]) : '';
        $isNumericReal = false;
        $value = str_replace(' ', '<br/>', $value);
        echo '<div class="table__row-' . $this->name . ' ' . ($this->fk ? 'table__row-id' : '') . ' table__row-' . ($isNumericReal ? 'num' : 'text') . '">' . $value . '</div>';

    }

}

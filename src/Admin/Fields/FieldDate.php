<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Plugins\UI\UI;
use Simflex\Core\DB;

class FieldDate extends Field
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->defaultValue = $this->e2n ? '' : date('Y-m-d');
        if (!empty($this->params['defaultValue'])) {
            $this->defaultValue = $this->params['defaultValue'];
        }
    }

    public function loadUI(bool $onForm = false): void
    {
        if ($onForm) {
            UI::datePicker();
        }
    }

    public function input($value): string
    {
        $value = \Simflex\Core\Time::normal($value ?: time());
        $classes = ['form-control'];
        if (!$this->readonly) {
            $classes[] = 'form-datepicker';
        }
        return '<div id="calendar" class="form-control form-control--sm">
                                    <input value="'.date('Y-m-d', strtotime($value)).'" name="' . $this->inputName() . '" placeholder="'.$this->placeholder.'" type="date" class="form-control__input">
                                </div>';
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : '';
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4}$@', $value)) {
            $value = substr($value, -4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2);
        }
        return $this->e2n && $value === '' ? null : '' . DB::escape($value) . '';
    }

    public function show($row): void
    {
        $value = $row[$this->name] ? \Simflex\Core\Time::userfull($row[$this->name]) : '';
        $isNumericReal = false;
        echo '<div class="table__row-' . $this->name . ' ' . ($this->fk ? 'table__row-id' : '') . ' table__row-' . ($isNumericReal ? 'num' : 'text') . '">' . $value . '</div>';

    }

    public static function typeLabel(): string
    {
        return 'Дата';
    }

    public static function typeParams(): array
    {
        return [];
    }
}

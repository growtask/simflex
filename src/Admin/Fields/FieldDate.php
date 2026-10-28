<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Plugins\UI\UI;
use Simflex\DB\DB;
use Simflex\Helpers\Time;

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

    public function loadUI(bool $onForm = false): string
    {
        if ($onForm) {
            UI::datePicker();
        }
        return '';
    }

    public function input($value): string
    {
        $value = Time::normal($value ?: time());
        return $this->render('date', [
            'value' => date('Y-m-d', strtotime($value)),
            'name' => $this->inputName(),
            'placeholder' => $this->placeholder,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = $_POST[$this->name] ?? '';
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4}$@', $value)) {
            $value = substr($value, -4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2);
        }
        return $this->e2n && $value === '' ? null : '' . DB::escape($value) . '';
    }

    public function show($row): string
    {
        return $this->cell($row[$this->name] ? Time::userFull($row[$this->name]) : '');
    }
}

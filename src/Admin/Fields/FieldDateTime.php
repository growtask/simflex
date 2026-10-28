<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\Admin\Plugins\UI\UI;

class FieldDateTime extends Field
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->defaultValue = !empty($this->params['defaultValue']) ? $this->params['defaultValue'] : date('Y-m-d H:i');
    }

    public function loadUI(bool $onForm = false): string
    {
        if ($onForm) {
            UI::dateTimePicker();
        }
        return '';
    }

    public function input($value): string
    {
        $value = \Simflex\Core\Helpers\Time::normal($value, true, false);
        return $this->render('datetime', [
            'value' => date('Y-m-d H:i:s', strtotime($value)),
            'name' => $this->inputName(),
            'placeholder' => $this->placeholder,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = (string)$this->getPosted();
        if ($simple) {
            return $value;
        }
        if (preg_match('@^[0-9]{2}.[0-9]{2}.[0-9]{4} [0-9]{2}.[0-9]{2}$@', $value)) {
            $value = substr($value, 6, 4) . '-' . substr($value, 3, 2) . '-' . substr($value, 0, 2) . ' ' . substr($value, 11, 2) . ':' . substr($value, 14, 2) . ':00';
        }
        return $this->e2n && $value === '' ? null : $value;
    }

    public function show($row): string
    {
        $value = $row[$this->name] ? \Simflex\Core\Helpers\Time::normal($row[$this->name]) : '';
        return $this->cell(str_replace(' ', '<br/>', $value));
    }
}

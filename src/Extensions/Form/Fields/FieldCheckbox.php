<?php

namespace Simflex\Extensions\Form\Fields;

class FieldCheckbox extends Field
{
    public function html(): string
    {
        $formName = $this->form->getName();
        $val = $_POST[$formName][$this->name] ?? $this->defval;
        return '<span class="plug-form-field"><input type="checkbox" name="' . htmlspecialchars($formName)
            . '[' . htmlspecialchars($this->name) . ']" ' . (empty($val) ? '' : 'checked="checked"') . ' /></span>';
    }
}

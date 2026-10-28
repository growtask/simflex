<?php

namespace Simflex\Core\Extensions\Form\Fields;

class FieldCheckbox extends Field
{
    public function html(): string
    {
        $formName = $this->form->getName();
        $val = $this->getPosted() ?? $this->defval;
        return '<span class="plug-form-field"><input type="checkbox" name="' . htmlspecialchars($formName)
            . '[' . htmlspecialchars($this->name) . ']" ' . (empty($val) ? '' : 'checked="checked"') . ' /></span>';
    }
}

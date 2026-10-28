<?php

namespace Simflex\Core\Extensions\Form\Fields;

class FieldPassword extends Field
{
    public function html(): string
    {
        $formName = $this->form->getName();
        $val = (string)($this->getPosted() ?? '');
        return '<span class="plug-form-field"><input type="password" name="' . htmlspecialchars($formName)
            . '[' . htmlspecialchars($this->name) . ']" value="' . htmlspecialchars($val) . '" /></span>';
    }
}

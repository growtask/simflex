<?php

namespace Simflex\Extensions\Form\Fields;

use Simflex\Extensions\Form\Fields\Field;

class FieldCheckbox extends Field
{

    public function html(): void
    {
        $formName = $this->form->getName();
        $val = $_POST[$formName][$this->name] ?? $this->defval;
        echo '<span class="plug-form-field"><input type="checkbox" name="', htmlspecialchars($formName), '[', htmlspecialchars($this->name), ']', '" ', empty($val) ? '' : 'checked="checked"', ' /></span>';
    }

}

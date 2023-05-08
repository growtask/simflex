<?php

namespace Simflex\Extensions\Form\Fields;

use Simflex\Extensions\Form\Fields\Field;

class FieldCheckbox extends Field
{

    public function html()
    {
        $val = isset($_POST[$this->form][$this->name]) ? $_POST[$this->form][$this->name] : $this->defval;
        echo '<span class="plug-form-field"><input type="checkbox" name="', $this->form, '[', $this->name, ']', '" ', empty($val) ? '' : 'checked="checked"', ' /></span>';
    }

}
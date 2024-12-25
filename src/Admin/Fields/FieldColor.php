<?php
namespace Simflex\Admin\Fields;

class FieldColor extends Field
{
    public function input($value)
    {
        $value = $this->value ?: $value;
        return '<div class="form-control form-control--sm">
                                    <input style="width:64px;height:64px;" name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '"
                                    ' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"')
            . ($this->readonly ? ' readonly' : '') . '
                                        type="color" class="">
                                </div>';
    }
}
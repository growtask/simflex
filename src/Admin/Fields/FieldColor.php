<?php

namespace Simflex\Core\Admin\Fields;

class FieldColor extends Field
{
    public function input($value): string
    {
        return $this->render('color', ['value' => $this->value ?: $value] + $this->inputVars());
    }
}

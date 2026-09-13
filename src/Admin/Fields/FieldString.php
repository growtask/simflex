<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;

class FieldString extends Field
{

    public static function typeLabel(): string
    {
        return 'Строка';
    }

    public static function typeParams(): array
    {
        return [];
    }

}
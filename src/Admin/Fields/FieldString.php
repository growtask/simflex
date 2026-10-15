<?php

namespace Simflex\Admin\Fields;

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

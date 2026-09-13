<?php

namespace Simflex\Admin\Fields;

class FieldPath extends Field
{
    public static function typeLabel(): string
    {
        return 'Url-путь';
    }

    public static function typeParams(): array
    {
        return [];
    }
}

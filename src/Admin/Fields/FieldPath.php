<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;

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

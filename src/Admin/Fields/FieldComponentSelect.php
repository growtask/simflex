<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\ExtensionRegistry;

class FieldComponentSelect extends FieldExtensionSelect
{
    public static function typeLabel(): string
    {
        return 'Выбор компонента';
    }

    protected function options(): array
    {
        return ExtensionRegistry::components();
    }
}

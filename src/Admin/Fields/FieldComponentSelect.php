<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\ExtensionRegistry;

class FieldComponentSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        return ExtensionRegistry::components();
    }
}

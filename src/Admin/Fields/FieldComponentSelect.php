<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\System\ExtensionRegistry;

class FieldComponentSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        return ExtensionRegistry::components();
    }
}

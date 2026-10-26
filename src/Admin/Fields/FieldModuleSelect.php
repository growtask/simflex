<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\ExtensionRegistry;

class FieldModuleSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        return array_map(fn(array $module) => $module['name'], ExtensionRegistry::modules());
    }
}

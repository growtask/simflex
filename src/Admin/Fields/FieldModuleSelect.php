<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\System\ExtensionRegistry;

class FieldModuleSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        return array_map(fn(array $module) => $module['name'], ExtensionRegistry::modules());
    }
}

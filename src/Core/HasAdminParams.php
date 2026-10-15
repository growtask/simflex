<?php

namespace Simflex\Core;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamGroup;

/**
 * Module or component with settings edited in the admin panel.
 * Values are stored serialized in module_item.params / component.params.
 */
interface HasAdminParams
{
    /**
     * @return array<FieldDefinition|ParamGroup> FieldDefinition goes to the common "Параметры" block,
     *         ParamGroup makes its own block
     */
    public static function adminParams(): array;
}

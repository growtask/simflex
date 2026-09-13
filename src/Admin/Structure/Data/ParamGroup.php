<?php

namespace Simflex\Admin\Structure\Data;

/**
 * Group of settings of a module or component, rendered as a separate block in the admin form.
 * See \Simflex\Core\ExtensionBase::adminParams()
 */
readonly class ParamGroup
{
    /**
     * @param FieldDefinition[] $fields
     * @param string $pos Form column: left|right
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $fields = [],
        public string $pos = 'left',
    ) {
    }
}

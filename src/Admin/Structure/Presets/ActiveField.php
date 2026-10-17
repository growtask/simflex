<?php

namespace Simflex\Admin\Structure\Presets;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;

/**
 * "Активно" switch, on for the new rows
 */
readonly class ActiveField extends FieldDefinition
{
    public function __construct(
        string $name = 'active',
        string $label = 'Активно',
        int|string $width = '104',
        string $defaultValue = '1',
        bool $filter = true,
    ) {
        parent::__construct(
            name: $name,
            class: FieldBool::class,
            label: $label,
            params: [
                'main' => new FieldParams(
                    width: $width,
                    defaultValue: $defaultValue,
                    filter: $filter,
                ),
            ],
        );
    }
}

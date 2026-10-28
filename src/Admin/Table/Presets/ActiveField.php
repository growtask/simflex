<?php

namespace Simflex\Core\Admin\Table\Presets;

use Simflex\Core\Admin\Fields\FieldBool;
use Simflex\Core\Admin\Table\FieldDefinition;

/**
 * Переключатель «Активно», у новых записей включён
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
            width: $width,
            defaultValue: $defaultValue,
            filter: $filter,
        );
    }
}

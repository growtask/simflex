<?php

namespace Simflex\Admin\Structure\Presets;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;

/**
 * Primary key: auto increment id, hidden in the form
 */
readonly class IdField extends FieldDefinition
{
    public function __construct(
        string $name = 'id',
        int|string $width = '60',
        bool $filter = false,
        int|string $widthMob = 0,
    ) {
        parent::__construct(
            name: $name,
            class: FieldInt::class,
            label: 'ID',
            params: [
                'main' => new FieldParams(
                    pk: true,
                    e2n: true,
                    hidden: true,
                    width: $width,
                    filter: $filter,
                    widthMob: $widthMob,
                ),
            ],
        );
    }
}

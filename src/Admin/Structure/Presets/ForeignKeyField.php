<?php

namespace Simflex\Admin\Structure\Presets;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;

/**
 * Reference to a row of another table, chosen from a list of its rows.
 * Not required one may be empty (NULL)
 */
readonly class ForeignKeyField extends FieldDefinition
{
    /**
     * @param string $table Referenced table
     * @param string|null $key Its primary key, the same as $name if not set
     * @param string $display Its column shown instead of the id
     */
    public function __construct(
        string $name,
        string $label,
        string $table,
        ?string $key = null,
        string $display = 'name',
        string $help = '',
        int|string $width = 0,
        bool $required = false,
        bool $filter = true,
        int|string $widthMob = 0,
    ) {
        parent::__construct(
            name: $name,
            class: FieldInt::class,
            label: $label,
            help: $help,
            params: [
                'main' => new FieldParams(
                    e2n: !$required,
                    width: $width,
                    required: $required,
                    filter: $filter,
                    widthMob: $widthMob,
                    isFk: true,
                    fkTable: $table,
                    fkKey: $key ?? $name,
                    fkLabel: $display,
                ),
            ],
        );
    }
}

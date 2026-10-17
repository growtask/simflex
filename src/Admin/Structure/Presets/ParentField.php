<?php

namespace Simflex\Admin\Structure\Presets;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;

/**
 * Parent row of the same table: makes the table a tree. Empty (NULL) for the root rows
 */
readonly class ParentField extends FieldDefinition
{
    /**
     * @param string $table The table itself
     * @param string $key Its primary key
     * @param string $display Its column shown instead of the id
     */
    public function __construct(
        string $name,
        string $table,
        string $key,
        string $display = 'name',
        string $label = 'Родитель',
        bool $filter = false,
    ) {
        parent::__construct(
            name: $name,
            class: FieldInt::class,
            label: $label,
            params: [
                'main' => new FieldParams(
                    e2n: true,
                    filter: $filter,
                    isFk: true,
                    fkTable: $table,
                    fkKey: $key,
                    fkLabel: $display,
                    fkIsPid: true,
                ),
            ],
        );
    }
}

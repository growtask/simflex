<?php

namespace Simflex\Admin\Structure\Presets;

use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;

/**
 * Sort order of the rows ("№ п/п"), the rows are dragged in the list to change it
 */
readonly class NppField extends FieldDefinition
{
    public function __construct(
        string $name = 'npp',
        int|string $width = '107',
    ) {
        parent::__construct(
            name: $name,
            class: FieldNPP::class,
            label: '№ п/п',
            params: [
                'main' => new FieldParams(
                    width: $width,
                    defaultValue: '0',
                ),
            ],
        );
    }
}

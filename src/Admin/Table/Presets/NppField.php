<?php

namespace Simflex\Admin\Table\Presets;

use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Table\FieldDefinition;

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
            width: $width,
            defaultValue: '0',
        );
    }
}

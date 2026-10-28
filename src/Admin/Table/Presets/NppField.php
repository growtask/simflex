<?php

namespace Simflex\Core\Admin\Table\Presets;

use Simflex\Core\Admin\Fields\FieldNPP;
use Simflex\Core\Admin\Table\FieldDefinition;

/**
 * Порядок записей («№ п/п»): меняется перетаскиванием строк в списке
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

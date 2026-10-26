<?php

namespace Simflex\Admin\Table\Presets;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Table\FieldDefinition;

/**
 * Первичный ключ: автоинкрементный id, скрыт в форме
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
            pk: true,
            e2n: true,
            hidden: true,
            width: $width,
            filter: $filter,
            widthMob: $widthMob,
        );
    }
}

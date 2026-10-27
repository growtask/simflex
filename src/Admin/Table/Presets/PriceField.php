<?php

namespace Simflex\Admin\Table\Presets;

use Simflex\Admin\Fields\FieldDouble;
use Simflex\Admin\Table\FieldDefinition;

/**
 * Цена (FieldDouble): в списке с пробелами между разрядами, дробная часть — сколько знаков в значении.
 * Пустое значение сохраняется как NULL
 */
readonly class PriceField extends FieldDefinition
{
    public function __construct(
        string $name,
        string $label,
        string $help = '',
        int|string $width = 0,
        bool $required = false,
        bool $filter = false,
        string $pos = '',
        string $posGroup = '',
    ) {
        parent::__construct(
            name: $name,
            class: FieldDouble::class,
            label: $label,
            help: $help,
            e2n: true,
            width: $width,
            required: $required,
            filter: $filter,
            pos: $pos,
            posGroup: $posGroup,
            params: ['decimals' => '0', 'dec_point' => '.', 'thousands_sep' => ' '],
        );
    }
}

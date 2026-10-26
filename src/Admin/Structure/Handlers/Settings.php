<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\NppField;
use Simflex\Admin\Structure\Table;

class Settings extends Table
{
    public function __construct()
    {
        parent::__construct(
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'alias',
                    class: FieldString::class,
                    label: 'Алиас',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Наименование',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            widthMob: '200',
                        ),
                    ],
                ),
                new NppField(),
                new IdField(
                    name: 'setting_id',
                    width: '54',
                    filter: true,
                    widthMob: '40',
                ),
                new FieldDefinition(
                    name: 'value',
                    class: FieldText::class,
                    label: 'Значение',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'type',
                    class: FieldEnum::class,
                    label: 'Тип',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: 'string',
                            readonly: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'enum_values',
                    class: FieldString::class,
                    label: 'Значения enum',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            hidden: true,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

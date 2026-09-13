<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Settings extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'settings',
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
                new FieldDefinition(
                    name: 'npp',
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'setting_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            hidden: true,
                            width: '54',
                            filter: true,
                            widthMob: '40',
                        ),
                    ],
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

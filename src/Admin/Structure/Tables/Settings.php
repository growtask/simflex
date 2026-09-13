<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Settings extends Table
{
    public function name(): string
    {
        return 'settings';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'alias',
                    label: 'Алиас',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Наименование',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: '№ п/п',
                    class: FieldNPP::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'setting_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Значение',
                    class: FieldText::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: FieldEnum::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: 'string',
                            readonly: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'enum_values',
                    label: 'Значения enum',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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

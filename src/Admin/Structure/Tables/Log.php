<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Log extends Table
{
    public static function name(): string
    {
        return 'log';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'log',
            orderBy: 'log_id',
            orderDesc: true,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'action' => new FieldDefinition(
                    name: 'action',
                    label: 'Действие',
                    class: \Simflex\Admin\Fields\FieldEnum::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '180',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                        ),
                    ],
                ),
                'browser' => new FieldDefinition(
                    name: 'browser',
                    label: 'Браузер',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '250',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
                    ],
                ),
                'data' => new FieldDefinition(
                    name: 'data',
                    label: 'Информация',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
                    ],
                ),
                'datetime' => new FieldDefinition(
                    name: 'datetime',
                    label: 'Время',
                    class: \Simflex\Admin\Fields\FieldDateTime::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '150',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
                    ],
                ),
                'ip' => new FieldDefinition(
                    name: 'ip',
                    label: 'IP адрес',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '180',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
                    ],
                ),
                'log_id' => new FieldDefinition(
                    name: 'log_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            isFk: false,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

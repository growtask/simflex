<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Log extends Table
{
    public function name(): string
    {
        return 'log';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'log_id',
            orderDesc: true,
            fields: [
                new FieldDefinition(
                    name: 'log_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'datetime',
                    label: 'Время',
                    class: FieldDateTime::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '150',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'action',
                    label: 'Действие',
                    class: FieldEnum::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ip',
                    label: 'IP адрес',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'browser',
                    label: 'Браузер',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'data',
                    label: 'Информация',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

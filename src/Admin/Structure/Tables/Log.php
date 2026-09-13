<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Log extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'log',
            orderBy: 'log_id',
            orderDesc: true,
            fields: [
                new FieldDefinition(
                    name: 'log_id',
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldDateTime::class,
                    label: 'Время',
                    params: [
                        'main' => new FieldParams(
                            width: '150',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'action',
                    class: FieldEnum::class,
                    label: 'Действие',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ip',
                    class: FieldString::class,
                    label: 'IP адрес',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'browser',
                    class: FieldString::class,
                    label: 'Браузер',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'data',
                    class: FieldString::class,
                    label: 'Информация',
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

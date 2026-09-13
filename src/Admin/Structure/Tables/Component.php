<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Component extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'component',
            fields: [
                new FieldDefinition(
                    name: 'component_id',
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
                    name: 'class',
                    class: FieldString::class,
                    label: 'Класс',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    class: FieldString::class,
                    label: 'Параметры',
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

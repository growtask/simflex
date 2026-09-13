<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Component extends Table
{
    public function name(): string
    {
        return 'component';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'component_id',
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
                    name: 'class',
                    label: 'Класс',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
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

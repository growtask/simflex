<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Component extends Table
{
    public static function name(): string
    {
        return 'component';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'component',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'component_id' => new FieldDefinition(
                    name: 'component_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '1',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '60',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                        ],
                    ],
                ),
                'class' => new FieldDefinition(
                    name: 'class',
                    label: 'Класс',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '250',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
                'params' => new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 9,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

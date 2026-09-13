<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ComponentParam extends Table
{
    public static function name(): string
    {
        return 'component_param';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'component_param',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'cp_id' => new FieldDefinition(
                    name: 'cp_id',
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
                            'is_fk' => '',
                        ],
                    ],
                ),
                'component_id' => new FieldDefinition(
                    name: 'component_id',
                    label: 'Компонент',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 2,
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
                            'is_fk' => '1',
                            'fk_table' => 'component',
                            'fk_key' => 'component_id',
                            'fk_label' => 'name',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'param_pid' => new FieldDefinition(
                    name: 'param_pid',
                    label: 'PID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 1,
                            'hidden' => '0',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'is_fk' => 1,
                            'fk_table' => 'component_param',
                            'fk_key' => 'cp_id',
                            'fk_label' => 'label',
                            'fk_is_pid' => true,
                        ],
                    ],
                ),
                'position' => new FieldDefinition(
                    name: 'position',
                    label: 'Позиция',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '120',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
                    ],
                ),
                'label' => new FieldDefinition(
                    name: 'label',
                    label: 'Ярлык',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 7,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => 1,
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => '80',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
                    ],
                ),
                'help' => new FieldDefinition(
                    name: 'help',
                    label: 'Подсказка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 9,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
                'params' => new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 10,
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
                'field_type' => new FieldDefinition(
                    name: 'field_type',
                    label: 'Тип поля',
                    class: \Simflex\Admin\Fields\FieldTypeSelect::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '150',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => 'onChangeField(this)',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

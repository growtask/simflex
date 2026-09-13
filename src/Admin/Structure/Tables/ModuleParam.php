<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ModuleParam extends Table
{
    public static function name(): string
    {
        return 'module_param';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'module_param',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'mp_id' => new FieldDefinition(
                    name: 'mp_id',
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
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '40',
                            'pos' => '',
                            'pos_group' => '',
                            'is_fk' => '0',
                            'fk_table' => '',
                            'fk_key' => '',
                            'fk_label' => '',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'module_id' => new FieldDefinition(
                    name: 'module_id',
                    label: 'Модуль',
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
                            'required' => '0',
                            'filter' => '1',
                            'fk' => '',
                            'onchange' => '',
                            'is_fk' => '1',
                            'fk_table' => 'module',
                            'fk_key' => 'module_id',
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
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '120',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '1',
                            'fk' => '',
                            'onchange' => '',
                            'is_fk' => '1',
                            'fk_table' => 'module_param',
                            'fk_key' => 'mp_id',
                            'fk_label' => 'label',
                            'fk_is_pid' => '1',
                        ],
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '200',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'label' => new FieldDefinition(
                    name: 'label',
                    label: 'Ярлык',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
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
                'params' => new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 12,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '1',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                            'onchange' => '',
                        ],
                    ],
                ),
                'position' => new FieldDefinition(
                    name: 'position',
                    label: 'Позиция',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '100',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                            'onchange' => '',
                        ],
                    ],
                ),
                'help' => new FieldDefinition(
                    name: 'help',
                    label: 'Подсказка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 7,
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
                'field_type' => new FieldDefinition(
                    name: 'field_type',
                    label: 'Тип поля',
                    class: \Simflex\Admin\Fields\FieldTypeSelect::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => 'onChangeField(this)',
                        ],
                    ],
                ),
            ],
            params: [
                'module_param_main' => new ParamDefinition(
                    name: 'module_param_main',
                    label: 'Параметры',
                    class: null,
                    paramId: 31,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '',
                    params: [],
                ),
                'default_value' => new ParamDefinition(
                    name: 'default_value',
                    label: 'Значение по умолчанию',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    paramId: 32,
                    paramPid: 31,
                    pos: '',
                    defaultValue: '',
                    params: [],
                ),
            ],
        );
    }
}

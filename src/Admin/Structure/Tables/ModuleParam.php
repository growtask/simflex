<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
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
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '40',
                            pos: '',
                            posGroup: '',
                            isFk: false,
                            fkTable: '',
                            fkKey: '',
                            fkLabel: '',
                            fkIsPid: false,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            fk: '',
                            onchange: '',
                            isFk: true,
                            fkTable: 'module',
                            fkKey: 'module_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '120',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            fk: '',
                            onchange: '',
                            isFk: true,
                            fkTable: 'module_param',
                            fkKey: 'mp_id',
                            fkLabel: 'label',
                            fkIsPid: true,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '200',
                            pos: '',
                            posGroup: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: 1,
                            defaultValue: '',
                            required: false,
                            filter: false,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '80',
                            defaultValue: '',
                            required: false,
                            filter: false,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: true,
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            fk: '',
                            onchange: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '100',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            fk: '',
                            onchange: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: 'onChangeField(this)',
                        ),
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

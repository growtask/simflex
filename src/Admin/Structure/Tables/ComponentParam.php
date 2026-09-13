<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
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
                'component_id' => new FieldDefinition(
                    name: 'component_id',
                    label: 'Компонент',
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
                            required: true,
                            filter: false,
                            onchange: '',
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
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
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            isFk: true,
                            fkTable: 'component_param',
                            fkKey: 'cp_id',
                            fkLabel: 'label',
                            fkIsPid: true,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '120',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: false,
                        ),
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
                'help' => new FieldDefinition(
                    name: 'help',
                    label: 'Подсказка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 9,
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
                'params' => new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 10,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: true,
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
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '150',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: 'onChangeField(this)',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

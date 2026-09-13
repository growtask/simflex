<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldTypeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ModuleParam extends Table
{
    public function name(): string
    {
        return 'module_param';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'mp_id',
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
                            widthMob: '40',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: FieldNPP::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '80',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'module_id',
                    label: 'Модуль',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            filter: true,
                            isFk: true,
                            fkTable: 'module',
                            fkKey: 'module_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'position',
                    label: 'Позиция',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '100',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'param_pid',
                    label: 'PID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '120',
                            filter: true,
                            isFk: true,
                            fkTable: 'module_param',
                            fkKey: 'mp_id',
                            fkLabel: 'label',
                            fkIsPid: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'field_type',
                    label: 'Тип поля',
                    class: FieldTypeSelect::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '1',
                            onchange: 'onChangeField(this)',
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
                            filter: true,
                            widthMob: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'label',
                    label: 'Ярлык',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: 1,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'help',
                    label: 'Подсказка',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(),
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
                            hidden: true,
                        ),
                    ],
                ),
            ],
            params: [
                new ParamDefinition(
                    name: 'module_param_main',
                    label: 'Параметры',
                    paramId: 31,
                    paramPid: '',
                    pos: 'right',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'default_value',
                    label: 'Значение по умолчанию',
                    class: FieldString::class,
                    paramId: 32,
                    paramPid: 31,
                    params: [],
                ),
            ],
        );
    }
}

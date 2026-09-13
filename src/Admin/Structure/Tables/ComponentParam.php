<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldTypeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class ComponentParam extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'component_param',
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'cp_id',
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
                    name: 'npp',
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '80',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'component_id',
                    class: FieldInt::class,
                    label: 'Компонент',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'param_pid',
                    class: FieldInt::class,
                    label: 'PID',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            isFk: true,
                            fkTable: 'component_param',
                            fkKey: 'cp_id',
                            fkLabel: 'label',
                            fkIsPid: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'position',
                    class: FieldString::class,
                    label: 'Позиция',
                    params: [
                        'main' => new FieldParams(
                            width: '120',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'field_type',
                    class: FieldTypeSelect::class,
                    label: 'Тип поля',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '150',
                            onchange: 'onChangeField(this)',
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
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'label',
                    class: FieldString::class,
                    label: 'Ярлык',
                    params: [
                        'main' => new FieldParams(
                            width: 1,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'help',
                    class: FieldString::class,
                    label: 'Подсказка',
                    params: [
                        'main' => new FieldParams(),
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

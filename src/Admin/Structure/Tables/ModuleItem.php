<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ModuleItem extends Table
{
    public function name(): string
    {
        return 'module_item';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '104',
                            defaultValue: '1',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'item_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            hidden: true,
                            width: '80',
                            filter: true,
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
                            width: '107',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    label: 'Наименование',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            widthMob: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'posname',
                    label: 'Позиция',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            filter: true,
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
                            width: '200',
                            filter: true,
                            onchange: 'onChangeModule(this)',
                            isFk: true,
                            fkTable: 'module',
                            fkKey: 'module_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'menu_id',
                    label: 'Меню',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '200',
                            filter: true,
                            isFk: true,
                            fkTable: 'menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: FieldText::class,
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
                    name: 'module_item_main',
                    label: 'Параметры',
                    paramId: 27,
                    paramPid: '',
                    pos: 'right',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'is_title',
                    label: 'Показывать заголовок',
                    class: FieldBool::class,
                    paramId: 28,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '1',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'is_wrap',
                    label: 'Выводить обертку',
                    class: FieldBool::class,
                    paramId: 29,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '1',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'cssclass',
                    label: 'CSS Класс',
                    class: FieldString::class,
                    paramId: 30,
                    paramPid: '',
                    pos: 'right',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
            ],
        );
    }
}

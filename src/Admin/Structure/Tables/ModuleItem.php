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
use Simflex\Admin\Structure\Table;

class ModuleItem extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'module_item',
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
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
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Наименование',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            widthMob: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'posname',
                    class: FieldString::class,
                    label: 'Позиция',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'module_id',
                    class: FieldInt::class,
                    label: 'Модуль',
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
                    class: FieldInt::class,
                    label: 'Меню',
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
                    class: FieldText::class,
                    label: 'Параметры',
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

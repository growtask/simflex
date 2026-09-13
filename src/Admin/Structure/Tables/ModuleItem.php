<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ModuleItem extends Table
{
    public static function name(): string
    {
        return 'module_item';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'module_item',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'item_id' => new FieldDefinition(
                    name: 'item_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: -2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: false,
                            hidden: true,
                            width: '80',
                            defaultValue: '',
                            required: false,
                            filter: true,
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
                            width: '200',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            fk: '',
                            onchange: 'onChangeModule(this)',
                            isFk: true,
                            fkTable: 'module',
                            fkKey: 'module_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'menu_id' => new FieldDefinition(
                    name: 'menu_id',
                    label: 'Меню',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '200',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            isFk: true,
                            fkTable: 'menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'posname' => new FieldDefinition(
                    name: 'posname',
                    label: 'Позиция',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '200',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'active' => new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: -2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '104',
                            defaultValue: '1',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: -1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '107',
                            defaultValue: '0',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Наименование',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 0,
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
                'params' => new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 4,
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
            ],
            params: [
                'module_item_main' => new ParamDefinition(
                    name: 'module_item_main',
                    label: 'Параметры',
                    class: null,
                    paramId: 27,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '',
                    params: [],
                ),
                'is_title' => new ParamDefinition(
                    name: 'is_title',
                    label: 'Показывать заголовок',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    paramId: 28,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '1',
                    params: [],
                ),
                'is_wrap' => new ParamDefinition(
                    name: 'is_wrap',
                    label: 'Выводить обертку',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    paramId: 29,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '1',
                    params: [],
                ),
                'cssclass' => new ParamDefinition(
                    name: 'cssclass',
                    label: 'CSS Класс',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    paramId: 30,
                    paramPid: '',
                    pos: 'right',
                    defaultValue: '',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: '',
                        ),
                    ],
                ),
            ],
        );
    }
}

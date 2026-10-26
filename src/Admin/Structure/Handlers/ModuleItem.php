<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Base\ModuleItem as ModuleItemDriver;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldModuleSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\ForeignKeyField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\NppField;
use Simflex\Admin\Structure\Table;

class ModuleItem extends Table
{
    public function __construct()
    {
        parent::__construct(
            orderBy: 'npp',
            class: ModuleItemDriver::class,
            fields: [
                new ActiveField(),
                new IdField(
                    name: 'item_id',
                    width: '80',
                    filter: true,
                    widthMob: '40',
                ),
                new NppField(),
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
                    name: 'module',
                    class: FieldModuleSelect::class,
                    label: 'Модуль',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            filter: true,
                            onchange: 'onChangeModule(this)',
                        ),
                    ],
                ),
                new ForeignKeyField(
                    name: 'menu_id',
                    label: 'Меню',
                    table: 'menu',
                    width: '200',
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
                    params: [],
                ),
                new ParamDefinition(
                    name: 'is_wrap',
                    label: 'Выводить обертку',
                    class: FieldBool::class,
                    paramId: 29,
                    paramPid: '',
                    pos: 'right',
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

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldAlias;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldContentTemplateSelect;
use Simflex\Admin\Fields\FieldDate;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPath;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\NppField;
use Simflex\Admin\Structure\Presets\ParentField;
use Simflex\Admin\Structure\Table;
use Simflex\Extensions\Content\Admin\AdminContent;

class Content extends Table
{
    public function __construct()
    {
        parent::__construct(
            class: AdminContent::class,
            fields: [
                new ActiveField(),
                new IdField(name: 'content_id', filter: true),
                new FieldDefinition(
                    name: 'template',
                    class: FieldContentTemplateSelect::class,
                    label: 'Шаблон',
                    help: 'Шаблоны страниц объявляются в provider/templates.php',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '176',
                            filter: true,
                        ),
                    ],
                ),
                new NppField(),
                new ParentField(
                    name: 'pid',
                    table: 'content',
                    key: 'content_id',
                    display: 'title',
                ),
                new FieldDefinition(
                    name: 'date',
                    class: FieldDate::class,
                    label: 'Дата',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'title',
                    class: FieldString::class,
                    label: 'Заголовок',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            widthMob: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'alias',
                    class: FieldAlias::class,
                    label: 'Алиас',
                    params: [
                        'main' => new FieldParams(
                            source: 'title',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'path',
                    class: FieldPath::class,
                    label: 'Путь',
                    params: [
                        'main' => new FieldParams(
                            readonly: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'short',
                    class: FieldText::class,
                    label: 'Коротко',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'text',
                    class: FieldText::class,
                    label: 'Текст',
                    params: [
                        'main' => new FieldParams(
                            editorFull: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    class: FieldString::class,
                    label: 'Параметры',
                    params: [
                        'main' => new FieldParams(
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'photo',
                    class: FieldImage::class,
                    label: 'Фото',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '88',
                            widthMob: '50',
                            path: 'content',
                            small: '200x150',
                            large: '800x600',
                        ),
                    ],
                ),
            ],
            params: [
                new ParamDefinition(
                    name: 'content_main',
                    label: 'Вывод данных',
                    paramId: 18,
                    paramPid: '',
                    pos: 'right',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'tpl',
                    label: 'Шаблон вывода',
                    class: FieldString::class,
                    paramId: 19,
                    paramPid: 18,
                    defaultValue: 'mod_list.tpl',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'cnt_limit',
                    label: 'Количество',
                    class: FieldInt::class,
                    paramId: 20,
                    paramPid: 18,
                    defaultValue: '3',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'content_view',
                    label: 'Внешний вид',
                    paramId: 21,
                    paramPid: '',
                    pos: 'right',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'date',
                    label: 'Дата',
                    class: FieldBool::class,
                    paramId: 22,
                    paramPid: 21,
                    params: [],
                ),
                new ParamDefinition(
                    name: 'short',
                    label: 'Анонс',
                    class: FieldBool::class,
                    paramId: 23,
                    paramPid: 21,
                    defaultValue: '0',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'more',
                    label: 'Кнопка далее',
                    class: FieldBool::class,
                    paramId: 24,
                    paramPid: 21,
                    defaultValue: '0',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'more_text',
                    label: 'Текст на кнопке далее',
                    class: FieldString::class,
                    paramId: 25,
                    paramPid: 21,
                    defaultValue: 'Читать далее',
                    params: [],
                ),
                new ParamDefinition(
                    name: 'hide_title',
                    label: 'Не показывать заголовок',
                    class: FieldBool::class,
                    paramId: 26,
                    paramPid: 21,
                    defaultValue: '0',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: '0',
                        ),
                    ],
                ),
            ],
        );
    }
}

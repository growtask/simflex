<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ContentTemplate extends Table
{
    public static function name(): string
    {
        return 'content_template';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'content_template',
            orderBy: '',
            orderDesc: false,
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            class: '',
            fields: [
                'template_id' => new FieldDefinition(
                    name: 'template_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldString::class,
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
                            'is_fk' => '',
                        ],
                    ],
                ),
                'template_name' => new FieldDefinition(
                    name: 'template_name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
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
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
                'template_path' => new FieldDefinition(
                    name: 'template_path',
                    label: 'Path',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
                    help: 'relative path from Extensions/Content/tpl. for example "mainPage.tpl" or "news/item.tpl"',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

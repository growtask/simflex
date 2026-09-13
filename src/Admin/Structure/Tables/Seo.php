<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Seo extends Table
{
    public static function name(): string
    {
        return 'seo';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'seo',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'seo_id' => new FieldDefinition(
                    name: 'seo_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '1',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '54',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '60',
                            'pos' => '',
                            'pos_group' => '',
                            'is_fk' => '0',
                            'fk_table' => '',
                            'fk_key' => '',
                            'fk_label' => '',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'seo_pid' => new FieldDefinition(
                    name: 'seo_pid',
                    label: 'PID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 1,
                            'hidden' => '0',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'is_fk' => 1,
                            'fk_table' => 'seo',
                            'fk_key' => 'seo_id',
                            'fk_label' => 'title',
                            'fk_is_pid' => true,
                        ],
                    ],
                ),
                'link' => new FieldDefinition(
                    name: 'link',
                    label: 'Ссылка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '300',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
                'title' => new FieldDefinition(
                    name: 'title',
                    label: 'Заголовок',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 4,
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
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '180',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'description' => new FieldDefinition(
                    name: 'description',
                    label: 'Описание',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => 0,
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
                    ],
                ),
                'keywords' => new FieldDefinition(
                    name: 'keywords',
                    label: 'Keywords',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => 0,
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

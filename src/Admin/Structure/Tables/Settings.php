<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Settings extends Table
{
    public static function name(): string
    {
        return 'settings';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'settings',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'setting_id' => new FieldDefinition(
                    name: 'setting_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '1',
                            'e2n' => '0',
                            'hidden' => '1',
                            'width' => '54',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '40',
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
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '107',
                            'defaultValue' => '0',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '200',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'alias' => new FieldDefinition(
                    name: 'alias',
                    label: 'Алиас',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '200',
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
                'value' => new FieldDefinition(
                    name: 'value',
                    label: 'Значение',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '200',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '0',
                            'pos' => '',
                            'pos_group' => '',
                            'editor_mini' => '0',
                            'editor_full' => '0',
                        ],
                    ],
                ),
                'type' => new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: \Simflex\Admin\Fields\FieldEnum::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '0',
                            'defaultValue' => 'string',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '1',
                            'style_cell' => '',
                        ],
                    ],
                ),
                'enum_values' => new FieldDefinition(
                    name: 'enum_values',
                    label: 'Значения enum',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

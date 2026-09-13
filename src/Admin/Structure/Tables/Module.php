<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Module extends Table
{
    public static function name(): string
    {
        return 'module';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'module',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'module_id' => new FieldDefinition(
                    name: 'module_id',
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
                            'width' => '50',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
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
                'class' => new FieldDefinition(
                    name: 'class',
                    label: 'Класс',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 1,
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
                            'filter' => '0',
                            'onchange' => '',
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
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '210',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'type' => new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: \Simflex\Admin\Fields\FieldEnum::class,
                    npp: 9,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '100',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'fk' => '',
                            'onchange' => '',
                        ],
                    ],
                ),
                'postexec' => new FieldDefinition(
                    name: 'postexec',
                    label: 'Выполнять после контента',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 8,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '250',
                            'defaultValue' => '0',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

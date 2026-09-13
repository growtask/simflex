<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class AdminMenu extends Table
{
    public static function name(): string
    {
        return 'admin_menu';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'admin_menu',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'menu_id' => new FieldDefinition(
                    name: 'menu_id',
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
                            'is_fk' => '0',
                            'fk_table' => '',
                            'fk_key' => '',
                            'fk_label' => '',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'menu_pid' => new FieldDefinition(
                    name: 'menu_pid',
                    label: 'Родитель',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '0',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '1',
                            'fk' => 'admin_menu.menu_id.name',
                            'onchange' => '',
                            'is_fk' => '1',
                            'fk_table' => 'admin_menu',
                            'fk_key' => 'menu_id',
                            'fk_label' => 'name',
                            'fk_is_pid' => '1',
                        ],
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 3,
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
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '120',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'fk' => 'user_priv.priv_id.name',
                            'onchange' => '',
                            'is_fk' => '1',
                            'fk_table' => 'user_priv',
                            'fk_key' => 'priv_id',
                            'fk_label' => 'name',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 5,
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
                            'width_mob' => '215',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'link' => new FieldDefinition(
                    name: 'link',
                    label: 'Ссылка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '400',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '576',
                            'width_mob' => '0',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
                'model' => new FieldDefinition(
                    name: 'model',
                    label: 'Модель',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 7,
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
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
                'icon' => new FieldDefinition(
                    name: 'icon',
                    label: 'Иконка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 8,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '0',
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

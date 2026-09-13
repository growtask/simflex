<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserRole extends Table
{
    public static function name(): string
    {
        return 'user_role';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'user_role',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'role_id' => new FieldDefinition(
                    name: 'role_id',
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
                            'width' => '54',
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
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '140',
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
                'active' => new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '104',
                            'defaultValue' => '1',
                            'required' => '0',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 4,
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
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
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
                            'width_mob' => '215',
                            'pos' => '',
                            'pos_group' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

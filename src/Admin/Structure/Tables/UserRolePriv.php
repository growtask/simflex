<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserRolePriv extends Table
{
    public static function name(): string
    {
        return 'user_role_priv';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'user_role_priv',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'id' => new FieldDefinition(
                    name: 'id',
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
                            'width' => '60',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                        ],
                    ],
                ),
                'role_id' => new FieldDefinition(
                    name: 'role_id',
                    label: 'Роль',
                    class: \Simflex\Admin\Fields\FieldInt::class,
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
                            'filter' => '1',
                            'fk' => 'user_role.role_id.name',
                            'onchange' => '',
                            'is_fk' => '1',
                            'fk_table' => 'user_role',
                            'fk_key' => 'role_id',
                            'fk_label' => 'name',
                            'fk_is_pid' => '0',
                        ],
                    ],
                ),
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 3,
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
            ],
            params: [
            ],
        );
    }
}

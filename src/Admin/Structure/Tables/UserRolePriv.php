<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
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
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            fk: '',
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            fk: 'user_role.role_id.name',
                            onchange: '',
                            isFk: true,
                            fkTable: 'user_role',
                            fkKey: 'role_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
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
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: true,
                            filter: true,
                            fk: 'user_priv.priv_id.name',
                            onchange: '',
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

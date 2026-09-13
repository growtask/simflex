<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserRolePriv extends Table
{
    public function name(): string
    {
        return 'user_role_priv';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'role_id',
                    label: 'Роль',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            filter: true,
                            fk: 'user_role.role_id.name',
                            isFk: true,
                            fkTable: 'user_role',
                            fkKey: 'role_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            fk: 'user_priv.priv_id.name',
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

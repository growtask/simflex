<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class UserRolePriv extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'user_role_priv',
            fields: [
                new FieldDefinition(
                    name: 'id',
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldInt::class,
                    label: 'Роль',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            filter: true,
                            isFk: true,
                            fkTable: 'user_role',
                            fkKey: 'role_id',
                            fkLabel: 'name',
                            fk: 'user_role.role_id.name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'priv_id',
                    class: FieldInt::class,
                    label: 'Привилегия',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
                            fkLabel: 'name',
                            fk: 'user_priv.priv_id.name',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

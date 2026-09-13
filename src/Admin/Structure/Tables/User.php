<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class User extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'user',
            fields: [
                new FieldDefinition(
                    name: 'user_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '80',
                            filter: true,
                            widthMob: '50',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: '0',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'role_id',
                    class: FieldInt::class,
                    label: 'Роль',
                    params: [
                        'main' => new FieldParams(
                            required: true,
                            filter: true,
                            isFk: true,
                            fkTable: 'user_role',
                            fkKey: 'role_id',
                            fkLabel: 'name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'login',
                    class: FieldString::class,
                    label: 'Логин',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            required: true,
                            filter: true,
                            widthMob: '190',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'password',
                    class: FieldPassword::class,
                    label: 'Пароль',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'hash',
                    class: FieldString::class,
                    label: 'Хеш',
                    params: [
                        'main' => new FieldParams(
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'hash_admin',
                    class: FieldString::class,
                    label: 'Admin. Хеш',
                    params: [
                        'main' => new FieldParams(
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'email',
                    class: FieldString::class,
                    label: 'Email',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '1',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Имя',
                    params: [
                        'main' => new FieldParams(
                            width: '140',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'in_mailing',
                    class: FieldBool::class,
                    label: 'Подписан на рассылку',
                    params: [
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

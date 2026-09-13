<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class User extends Table
{
    public function name(): string
    {
        return 'user';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'user_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Активно',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: '0',
                            filter: true,
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
                    label: 'Логин',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Пароль',
                    class: FieldPassword::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'hash',
                    label: 'Хеш',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'hash_admin',
                    label: 'Admin. Хеш',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'email',
                    label: 'Email',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Имя',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '140',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'in_mailing',
                    label: 'Подписан на рассылку',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

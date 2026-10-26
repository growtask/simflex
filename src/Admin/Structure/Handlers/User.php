<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\ForeignKeyField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Table;

class User extends Table
{
    public function __construct()
    {
        parent::__construct(
            fields: [
                new IdField(
                    name: 'user_id',
                    width: '80',
                    filter: true,
                    widthMob: '50',
                ),
                new ActiveField(width: 0, defaultValue: '0'),
                new ForeignKeyField(
                    name: 'role_id',
                    label: 'Роль',
                    table: 'user_role',
                    required: true,
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

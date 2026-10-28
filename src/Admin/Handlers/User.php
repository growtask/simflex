<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\ForeignKeyField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Table;
use Simflex\Models\User as UserModel;

class User extends Table
{
    protected static string $model = UserModel::class;

    public static function getFields(): array
    {
        return [
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
                width: '200',
                required: true,
                filter: true,
                widthMob: '190',
            ),
            new FieldDefinition(
                name: 'password',
                class: FieldPassword::class,
                label: 'Пароль',
            ),
            new FieldDefinition(
                name: 'email',
                class: FieldString::class,
                label: 'Email',
                e2n: true,
                width: '1',
                filter: true,
            ),
            new FieldDefinition(
                name: 'name',
                class: FieldString::class,
                label: 'Имя',
                width: '140',
                filter: true,
            ),
            new FieldDefinition(
                name: 'in_mailing',
                class: FieldBool::class,
                label: 'Подписан на рассылку',
            ),
        ];
    }
}

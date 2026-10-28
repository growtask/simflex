<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Table;
use Simflex\Models\UserRole as UserRoleModel;

class UserRole extends Table
{
    protected static string $model = UserRoleModel::class;

    public static function getFields(): array
    {
        return [
            new IdField(
                name: 'role_id',
                width: '54',
                filter: true,
            ),
            new ActiveField(),
            new FieldDefinition(
                name: 'privilege',
                class: FieldPrivilegeSelect::class,
                label: 'Привилегия',
                help: 'Кто управляет ролью: роль и её пользователей видят и редактируют только пользователи с этой привилегией (и разработчики). Привилегии, которые роль даёт своим пользователям, задаются в разделе «Привилегии ролей»',
                width: '200',
                required: true,
                filter: true,
            ),
            new FieldDefinition(
                name: 'name',
                class: FieldString::class,
                label: 'Название',
                width: '1',
                required: true,
                filter: true,
                widthMob: '215',
            ),
        ];
    }
}

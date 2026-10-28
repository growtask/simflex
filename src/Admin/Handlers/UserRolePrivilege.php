<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\ForeignKeyField;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\System\Privilege;
use Simflex\Core\Models\UserRolePrivilege as UserRolePrivilegeModel;

/**
 * Привилегии ролей. Только для разработчиков: здесь можно выдать любую привилегию, в том числе dev
 */
class UserRolePrivilege extends Table
{
    protected static string $model = UserRolePrivilegeModel::class;
    protected static ?string $privilegeAdd = Privilege::DEV;
    protected static ?string $privilegeEdit = Privilege::DEV;
    protected static ?string $privilegeDelete = Privilege::DEV;

    public static function getFields(): array
    {
        return [
            new IdField(),
            new ForeignKeyField(
                name: 'role_id',
                label: 'Роль',
                table: 'user_role',
                help: 'Роль, пользователи которой получают привилегию',
                width: '300',
                required: true,
            ),
            new FieldDefinition(
                name: 'privilege',
                class: FieldPrivilegeSelect::class,
                label: 'Привилегия',
                help: 'Привилегия, которую получают все пользователи роли. Не путать с полем «Привилегия» в разделе «Роли»: там указано, кто управляет ролью',
                width: '1',
                required: true,
                filter: true,
            ),
        ];
    }
}

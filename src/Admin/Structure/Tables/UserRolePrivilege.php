<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ForeignKeyField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Table;
use Simflex\Core\Privilege;

/**
 * Privileges of the roles. Only for the developers: any privilege, the dev one too, can be given here
 */
class UserRolePrivilege extends Table
{
    public function __construct()
    {
        parent::__construct(
            privilegeAdd: Privilege::DEV,
            privilegeEdit: Privilege::DEV,
            privilegeDelete: Privilege::DEV,
            fields: [
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
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

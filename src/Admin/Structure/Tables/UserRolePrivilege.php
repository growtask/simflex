<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
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
                    help: 'Роль, пользователи которой получают привилегию',
                    params: [
                        'main' => new FieldParams(
                            width: '300',
                            required: true,
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

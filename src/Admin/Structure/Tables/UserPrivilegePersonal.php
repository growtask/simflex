<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;
use Simflex\Core\Privilege;

/**
 * Privileges given to a user personally, besides the ones of the user's role.
 * Only for the developers: any privilege, the dev one too, can be given here
 */
class UserPrivilegePersonal extends Table
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
                    name: 'user_id',
                    class: FieldInt::class,
                    label: 'Пользователь',
                    help: 'Пользователь, которому привилегия выдаётся лично, сверх привилегий его роли',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            required: true,
                            filter: true,
                            isFk: true,
                            fkTable: 'user',
                            fkKey: 'user_id',
                            fkLabel: 'login',
                            fk: 'user.user_id.login',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'privilege',
                    class: FieldPrivilegeSelect::class,
                    label: 'Привилегия',
                    help: 'Привилегия, которую получает пользователь',
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

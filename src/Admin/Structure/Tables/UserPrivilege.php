<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Base\Privileges as PrivilegesDriver;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

/**
 * Privileges are declared in code (\Simflex\Core\Privilege), the optional table keeps the activity switched
 * in the admin panel. The section has its own handler, \Simflex\Admin\Base\Privileges
 */
class UserPrivilege extends Table
{
    public function __construct()
    {
        parent::__construct(
            class: PrivilegesDriver::class,
            fields: [
                new FieldDefinition(
                    name: 'privilege_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldPrivilegeSelect::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
                    params: [
                        'main' => new FieldParams(
                            defaultValue: '1',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

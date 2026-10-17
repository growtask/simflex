<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Base\Privileges as PrivilegesDriver;
use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\IdField;
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
                new IdField(name: 'privilege_id', width: 0),
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
                new ActiveField(width: 0, filter: false),
            ],
            params: [
            ],
        );
    }
}

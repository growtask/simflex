<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class UserRole extends Table
{
    public function __construct()
    {
        parent::__construct(
            fields: [
                new FieldDefinition(
                    name: 'role_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '54',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
                    params: [
                        'main' => new FieldParams(
                            width: '104',
                            defaultValue: '1',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'privilege',
                    class: FieldPrivilegeSelect::class,
                    label: 'Привилегия',
                    help: 'Кто управляет ролью: роль и её пользователей видят и редактируют только пользователи с этой привилегией (и разработчики). Привилегии, которые роль даёт своим пользователям, задаются в разделе «Привилегии ролей»',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            widthMob: '215',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

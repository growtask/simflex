<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Table;

class UserRole extends Table
{
    public function __construct()
    {
        parent::__construct(
            fields: [
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

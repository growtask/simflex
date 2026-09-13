<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserRole extends Table
{
    public static function name(): string
    {
        return 'user_role';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'user_role',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'role_id' => new FieldDefinition(
                    name: 'role_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '54',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '140',
                            defaultValue: '',
                            required: true,
                            filter: true,
                            fk: 'user_priv.priv_id.name',
                            onchange: '',
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'active' => new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '104',
                            defaultValue: '1',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '107',
                            defaultValue: '0',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: true,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '215',
                            pos: '',
                            posGroup: '',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

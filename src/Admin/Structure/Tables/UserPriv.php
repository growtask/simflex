<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserPriv extends Table
{
    public static function name(): string
    {
        return 'user_priv';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'user_priv',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '40',
                            pos: '',
                            posGroup: '',
                            isFk: false,
                            fkTable: '',
                            fkKey: '',
                            fkLabel: '',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'active' => new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '85',
                            defaultValue: '1',
                            required: false,
                            filter: true,
                            fk: '',
                            onchange: '',
                        ),
                    ],
                ),
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '80',
                            defaultValue: '0',
                            required: false,
                            filter: false,
                            fk: '',
                        ),
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '200',
                            defaultValue: '',
                            required: true,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '200',
                            pos: '',
                            posGroup: '',
                        ),
                    ],
                ),
                'comment' => new FieldDefinition(
                    name: 'comment',
                    label: 'Комментарий',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            fk: '',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

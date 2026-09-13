<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Module extends Table
{
    public static function name(): string
    {
        return 'module';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'module',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'module_id' => new FieldDefinition(
                    name: 'module_id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: false,
                            hidden: true,
                            width: '50',
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
                'class' => new FieldDefinition(
                    name: 'class',
                    label: 'Класс',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 1,
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
                        ),
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Наименование',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 0,
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
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '210',
                            pos: '',
                            posGroup: '',
                        ),
                    ],
                ),
                'type' => new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: \Simflex\Admin\Fields\FieldEnum::class,
                    npp: 9,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '100',
                            defaultValue: '',
                            required: true,
                            filter: true,
                            fk: '',
                            onchange: '',
                        ),
                    ],
                ),
                'postexec' => new FieldDefinition(
                    name: 'postexec',
                    label: 'Выполнять после контента',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 8,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '250',
                            defaultValue: '0',
                            required: false,
                            filter: false,
                            onchange: '',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Settings extends Table
{
    public static function name(): string
    {
        return 'settings';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'settings',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'setting_id' => new FieldDefinition(
                    name: 'setting_id',
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
                            width: '54',
                            defaultValue: '',
                            required: false,
                            filter: true,
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
                'npp' => new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: \Simflex\Admin\Fields\FieldNPP::class,
                    npp: 0,
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
                            filter: true,
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
                'alias' => new FieldDefinition(
                    name: 'alias',
                    label: 'Алиас',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 0,
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
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                        ),
                    ],
                ),
                'value' => new FieldDefinition(
                    name: 'value',
                    label: 'Значение',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 0,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '200',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '0',
                            pos: '',
                            posGroup: '',
                            editorMini: false,
                            editorFull: false,
                        ),
                    ],
                ),
                'type' => new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: \Simflex\Admin\Fields\FieldEnum::class,
                    npp: 1,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '0',
                            defaultValue: 'string',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: true,
                            styleCell: '',
                        ),
                    ],
                ),
                'enum_values' => new FieldDefinition(
                    name: 'enum_values',
                    label: 'Значения enum',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: true,
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

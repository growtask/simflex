<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Menu extends Table
{
    public static function name(): string
    {
        return 'menu';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'menu',
            orderBy: 'npp',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'menu_id' => new FieldDefinition(
                    name: 'menu_id',
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
                'menu_pid' => new FieldDefinition(
                    name: 'menu_pid',
                    label: 'Родитель',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 4,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            fk: 'menu.menu_id.name',
                            onchange: '',
                            isFk: true,
                            fkTable: 'menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: true,
                        ),
                    ],
                ),
                'component_id' => new FieldDefinition(
                    name: 'component_id',
                    label: 'Компонент',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '0',
                            defaultValue: '',
                            required: false,
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '200',
                            pos: '',
                            posGroup: '',
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
                            fkLabel: 'name',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'active' => new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 1,
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
                'hidden' => new FieldDefinition(
                    name: 'hidden',
                    label: 'Скрыть',
                    class: \Simflex\Admin\Fields\FieldBool::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '104',
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
                            width: '107',
                            defaultValue: '',
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
                    npp: 7,
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
                            screenWidth: '991',
                            widthMob: '200',
                            pos: '',
                            posGroup: '',
                        ),
                    ],
                ),
                'link' => new FieldDefinition(
                    name: 'link',
                    label: 'Ссылка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 8,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '300',
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
            ],
            params: [
            ],
        );
    }
}

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Seo extends Table
{
    public static function name(): string
    {
        return 'seo';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'seo',
            orderBy: '',
            orderDesc: false,
            privAdd: null,
            privEdit: null,
            privDelete: null,
            class: '',
            fields: [
                'seo_id' => new FieldDefinition(
                    name: 'seo_id',
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
                            widthMob: '60',
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
                'seo_pid' => new FieldDefinition(
                    name: 'seo_pid',
                    label: 'PID',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 2,
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
                            filter: false,
                            isFk: true,
                            fkTable: 'seo',
                            fkKey: 'seo_id',
                            fkLabel: 'title',
                            fkIsPid: true,
                        ),
                    ],
                ),
                'link' => new FieldDefinition(
                    name: 'link',
                    label: 'Ссылка',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '300',
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
                'title' => new FieldDefinition(
                    name: 'title',
                    label: 'Заголовок',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 4,
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
                            filter: true,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            screenWidth: '0',
                            widthMob: '180',
                            pos: '',
                            posGroup: '',
                        ),
                    ],
                ),
                'description' => new FieldDefinition(
                    name: 'description',
                    label: 'Описание',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 5,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: 0,
                            defaultValue: '',
                            required: false,
                            filter: false,
                        ),
                    ],
                ),
                'keywords' => new FieldDefinition(
                    name: 'keywords',
                    label: 'Keywords',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 6,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: 0,
                            defaultValue: '',
                            required: false,
                            filter: false,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Cron extends Table
{
    public static function name(): string
    {
        return 'cron';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'cron',
            orderBy: '',
            orderDesc: false,
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            class: '',
            fields: [
                'action' => new FieldDefinition(
                    name: 'action',
                    label: 'Действие',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 6,
                    help: 'Метод компонента или плагина',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '180',
                            defaultValue: '',
                            required: true,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
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
                            width: '85',
                            defaultValue: '1',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                        ),
                    ],
                ),
                'cparams' => new FieldDefinition(
                    name: 'cparams',
                    label: 'Параметры',
                    class: \Simflex\Admin\Fields\FieldText::class,
                    npp: 7,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '1',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            editorMini: false,
                            editorFull: false,
                        ),
                    ],
                ),
                'ext_id' => new FieldDefinition(
                    name: 'ext_id',
                    label: 'Расширение',
                    class: \Simflex\Admin\Fields\FieldInt::class,
                    npp: 4,
                    help: 'ID компонента',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '180',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
                            fkLabel: 'class',
                            fkIsPid: false,
                        ),
                    ],
                ),
                'id' => new FieldDefinition(
                    name: 'id',
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
                            isFk: false,
                        ),
                    ],
                ),
                'name' => new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
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
                        ),
                    ],
                ),
                'plugin_name' => new FieldDefinition(
                    name: 'plugin_name',
                    label: 'Плагин',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 5,
                    help: 'Название класса плагина',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: true,
                            hidden: false,
                            width: '180',
                            defaultValue: '',
                            required: false,
                            filter: false,
                            onchange: '',
                            readonly: false,
                            styleCell: '',
                        ),
                    ],
                ),
                'timing' => new FieldDefinition(
                    name: 'timing',
                    label: 'Время',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: 'Как в *nix crontab. Например */10 * * * *',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: false,
                            e2n: false,
                            hidden: false,
                            width: '160',
                            defaultValue: '',
                            required: true,
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

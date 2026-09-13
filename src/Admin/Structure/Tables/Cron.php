<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Cron extends Table
{
    public function name(): string
    {
        return 'cron';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '85',
                            defaultValue: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '60',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'timing',
                    label: 'Время',
                    class: FieldString::class,
                    help: 'Как в *nix crontab. Например */10 * * * *',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '160',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    label: 'Название',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ext_id',
                    label: 'Расширение',
                    class: FieldInt::class,
                    help: 'ID компонента',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '180',
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
                            fkLabel: 'class',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'plugin_name',
                    label: 'Плагин',
                    class: FieldString::class,
                    help: 'Название класса плагина',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '180',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'action',
                    label: 'Действие',
                    class: FieldString::class,
                    help: 'Метод компонента или плагина',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'cparams',
                    label: 'Параметры',
                    class: FieldText::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '1',
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

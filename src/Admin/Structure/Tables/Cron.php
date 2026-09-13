<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Cron extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'cron',
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
                    params: [
                        'main' => new FieldParams(
                            width: '85',
                            defaultValue: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'id',
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldString::class,
                    label: 'Время',
                    help: 'Как в *nix crontab. Например */10 * * * *',
                    params: [
                        'main' => new FieldParams(
                            width: '160',
                            required: true,
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
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ext_id',
                    class: FieldInt::class,
                    label: 'Расширение',
                    help: 'ID компонента',
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
                    class: FieldString::class,
                    label: 'Плагин',
                    help: 'Название класса плагина',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '180',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'action',
                    class: FieldString::class,
                    label: 'Действие',
                    help: 'Метод компонента или плагина',
                    params: [
                        'main' => new FieldParams(
                            width: '180',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'cparams',
                    class: FieldText::class,
                    label: 'Параметры',
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

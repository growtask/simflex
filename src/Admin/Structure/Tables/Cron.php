<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '180',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '85',
                            'defaultValue' => '1',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'editor_mini' => '0',
                            'editor_full' => '0',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '180',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'is_fk' => '1',
                            'fk_table' => 'component',
                            'fk_key' => 'component_id',
                            'fk_label' => 'class',
                            'fk_is_pid' => '0',
                        ],
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
                        'main' => [
                            'pk' => '1',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '60',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'is_fk' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => 0,
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '1',
                            'hidden' => '0',
                            'width' => '180',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '160',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserPrivPersonal extends Table
{
    public static function name(): string
    {
        return 'user_priv_personal';
    }

    public static function definition(): TableDefinition
    {
        return new TableDefinition(
            name: 'user_priv_personal',
            orderBy: '',
            orderDesc: false,
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            class: '',
            fields: [
                'id' => new FieldDefinition(
                    name: 'id',
                    label: 'ID',
                    class: \Simflex\Admin\Fields\FieldString::class,
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
                'user_id' => new FieldDefinition(
                    name: 'user_id',
                    label: 'Пользователь',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 2,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '250',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
                'priv_id' => new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: \Simflex\Admin\Fields\FieldString::class,
                    npp: 3,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '1',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Structure\Data\FieldDefinition;
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
                        'main' => [
                            'pk' => '1',
                            'e2n' => '1',
                            'hidden' => '1',
                            'width' => '60',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '40',
                            'pos' => '',
                            'pos_group' => '',
                            'is_fk' => '0',
                            'fk_table' => '',
                            'fk_key' => '',
                            'fk_label' => '',
                            'fk_is_pid' => '0',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '85',
                            'defaultValue' => '1',
                            'required' => '0',
                            'filter' => '1',
                            'fk' => '',
                            'onchange' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '80',
                            'defaultValue' => '0',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '200',
                            'defaultValue' => '',
                            'required' => '1',
                            'filter' => '0',
                            'onchange' => '',
                            'readonly' => '0',
                            'style_cell' => '',
                            'screen_width' => '0',
                            'width_mob' => '200',
                            'pos' => '',
                            'pos_group' => '',
                        ],
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
                        'main' => [
                            'pk' => '0',
                            'e2n' => '0',
                            'hidden' => '0',
                            'width' => '1',
                            'defaultValue' => '',
                            'required' => '0',
                            'filter' => '0',
                            'fk' => '',
                        ],
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

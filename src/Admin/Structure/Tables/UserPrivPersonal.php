<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserPrivPersonal extends Table
{
    public function name(): string
    {
        return 'user_priv_personal';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'id',
                    label: 'ID',
                    class: FieldString::class,
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
                    name: 'user_id',
                    label: 'Пользователь',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

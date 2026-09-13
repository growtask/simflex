<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class UserPriv extends Table
{
    public function name(): string
    {
        return 'user_priv';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'priv_id',
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
                            widthMob: '40',
                        ),
                    ],
                ),
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
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: FieldNPP::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '80',
                            defaultValue: '0',
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
                            width: '200',
                            required: true,
                            widthMob: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'comment',
                    label: 'Комментарий',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
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

<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class UserPriv extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'user_priv',
            fields: [
                new FieldDefinition(
                    name: 'priv_id',
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldBool::class,
                    label: 'Активно',
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
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '80',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Название',
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
                    class: FieldString::class,
                    label: 'Комментарий',
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

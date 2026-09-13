<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Module extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'module',
            fields: [
                new FieldDefinition(
                    name: 'module_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            hidden: true,
                            width: '50',
                            widthMob: '40',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Наименование',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            widthMob: '210',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'class',
                    class: FieldString::class,
                    label: 'Класс',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'postexec',
                    class: FieldBool::class,
                    label: 'Выполнять после контента',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'type',
                    class: FieldEnum::class,
                    label: 'Тип',
                    params: [
                        'main' => new FieldParams(
                            width: '100',
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

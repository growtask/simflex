<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Module extends Table
{
    public function name(): string
    {
        return 'module';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'module_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Наименование',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Класс',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            required: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'postexec',
                    label: 'Выполнять после контента',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'type',
                    label: 'Тип',
                    class: FieldEnum::class,
                    help: '',
                    placeholder: '',
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

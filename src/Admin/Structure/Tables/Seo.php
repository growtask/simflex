<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Seo extends Table
{
    public function name(): string
    {
        return 'seo';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            fields: [
                new FieldDefinition(
                    name: 'seo_id',
                    label: 'ID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '54',
                            filter: true,
                            widthMob: '60',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'seo_pid',
                    label: 'PID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            isFk: true,
                            fkTable: 'seo',
                            fkKey: 'seo_id',
                            fkLabel: 'title',
                            fkIsPid: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'link',
                    label: 'Ссылка',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '300',
                            required: true,
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'title',
                    label: 'Заголовок',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            filter: true,
                            widthMob: '180',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'description',
                    label: 'Описание',
                    class: FieldText::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'keywords',
                    label: 'Keywords',
                    class: FieldText::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}

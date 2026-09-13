<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ContentTemplate extends Table
{
    public function name(): string
    {
        return 'content_template';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'template_id',
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
                    name: 'template_name',
                    label: 'Название',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'template_path',
                    label: 'Path',
                    class: FieldString::class,
                    help: 'relative path from Extensions/Content/tpl. for example "mainPage.tpl" or "news/item.tpl"',
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

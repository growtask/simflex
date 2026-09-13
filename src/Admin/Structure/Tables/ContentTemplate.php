<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class ContentTemplate extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'content_template',
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'template_id',
                    class: FieldString::class,
                    label: 'ID',
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
                    class: FieldString::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'template_path',
                    class: FieldString::class,
                    label: 'Path',
                    help: 'relative path from Extensions/Content/tpl. for example "mainPage.tpl" or "news/item.tpl"',
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

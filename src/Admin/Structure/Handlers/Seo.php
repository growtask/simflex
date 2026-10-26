<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\ParentField;
use Simflex\Admin\Structure\Table;

class Seo extends Table
{
    public function __construct()
    {
        parent::__construct(
            fields: [
                new IdField(
                    name: 'seo_id',
                    width: '54',
                    filter: true,
                    widthMob: '60',
                ),
                new ParentField(
                    name: 'seo_pid',
                    table: 'seo',
                    key: 'seo_id',
                    display: 'title',
                ),
                new FieldDefinition(
                    name: 'link',
                    class: FieldString::class,
                    label: 'Ссылка',
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
                    class: FieldString::class,
                    label: 'Заголовок',
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
                    class: FieldText::class,
                    label: 'Описание',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'keywords',
                    class: FieldText::class,
                    label: 'Keywords',
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

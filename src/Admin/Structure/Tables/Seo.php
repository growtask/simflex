<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Seo extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'seo',
            fields: [
                new FieldDefinition(
                    name: 'seo_id',
                    class: FieldInt::class,
                    label: 'ID',
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
                    class: FieldInt::class,
                    label: 'PID',
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

<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldComponentSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\NppField;
use Simflex\Admin\Structure\Presets\ParentField;
use Simflex\Admin\Structure\Table;

class Menu extends Table
{
    public function __construct()
    {
        parent::__construct(
            orderBy: 'npp',
            fields: [
                new ActiveField(),
                new IdField(
                    name: 'menu_id',
                    width: '54',
                    filter: true,
                    widthMob: '40',
                ),
                new FieldDefinition(
                    name: 'hidden',
                    class: FieldBool::class,
                    label: 'Скрыть',
                    params: [
                        'main' => new FieldParams(
                            width: '104',
                            filter: true,
                        ),
                    ],
                ),
                new NppField(),
                new ParentField(
                    name: 'menu_pid',
                    table: 'menu',
                    key: 'menu_id',
                    filter: true,
                ),
                new FieldDefinition(
                    name: 'component',
                    class: FieldComponentSelect::class,
                    label: 'Компонент',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            filter: true,
                            widthMob: '200',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            required: true,
                            filter: true,
                            screenWidth: '991',
                            widthMob: '200',
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

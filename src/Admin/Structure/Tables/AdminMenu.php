<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldTableSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Presets\NppField;
use Simflex\Admin\Structure\Presets\ParentField;
use Simflex\Admin\Structure\Table;

class AdminMenu extends Table
{
    public function __construct()
    {
        parent::__construct(
            orderBy: 'npp',
            fields: [
                new IdField(
                    name: 'menu_id',
                    width: '54',
                    filter: true,
                ),
                new ParentField(
                    name: 'menu_pid',
                    table: 'admin_menu',
                    key: 'menu_id',
                    filter: true,
                ),
                new NppField(),
                new FieldDefinition(
                    name: 'privilege',
                    class: FieldPrivilegeSelect::class,
                    label: 'Привилегия',
                    params: [
                        'main' => new FieldParams(
                            width: '120',
                            required: true,
                            filter: true,
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
                            filter: true,
                            widthMob: '215',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'link',
                    class: FieldString::class,
                    label: 'Ссылка',
                    params: [
                        'main' => new FieldParams(
                            width: '400',
                            required: true,
                            filter: true,
                            screenWidth: '576',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'model',
                    class: FieldTableSelect::class,
                    label: 'Модель',
                    help: 'Таблица из реестра Admin/Structure/tables.php. Пусто — раздел с собственным классом',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'icon',
                    class: FieldString::class,
                    label: 'Иконка',
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

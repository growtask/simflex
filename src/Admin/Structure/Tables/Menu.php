<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class Menu extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'menu',
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'active',
                    class: FieldBool::class,
                    label: 'Активно',
                    params: [
                        'main' => new FieldParams(
                            width: '104',
                            defaultValue: '1',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'menu_id',
                    class: FieldInt::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '54',
                            filter: true,
                            widthMob: '40',
                        ),
                    ],
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
                new FieldDefinition(
                    name: 'npp',
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'menu_pid',
                    class: FieldInt::class,
                    label: 'Родитель',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            filter: true,
                            isFk: true,
                            fkTable: 'menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: true,
                            fk: 'menu.menu_id.name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'component_id',
                    class: FieldInt::class,
                    label: 'Компонент',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            filter: true,
                            widthMob: '200',
                            isFk: true,
                            fkTable: 'component',
                            fkKey: 'component_id',
                            fkLabel: 'name',
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

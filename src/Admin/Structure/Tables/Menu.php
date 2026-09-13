<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class Menu extends Table
{
    public function name(): string
    {
        return 'menu';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'npp',
            fields: [
                new FieldDefinition(
                    name: 'active',
                    label: 'Активно',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
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
                            widthMob: '40',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'hidden',
                    label: 'Скрыть',
                    class: FieldBool::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '104',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'npp',
                    label: '№ п/п',
                    class: FieldNPP::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'menu_pid',
                    label: 'Родитель',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            filter: true,
                            fk: 'menu.menu_id.name',
                            isFk: true,
                            fkTable: 'menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'component_id',
                    label: 'Компонент',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Название',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Ссылка',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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

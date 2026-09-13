<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class AdminMenu extends Table
{
    public function name(): string
    {
        return 'admin_menu';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            orderBy: 'npp',
            fields: [
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
                            fk: 'admin_menu.menu_id.name',
                            isFk: true,
                            fkTable: 'admin_menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: true,
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
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'priv_id',
                    label: 'Привилегия',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '120',
                            required: true,
                            filter: true,
                            fk: 'user_priv.priv_id.name',
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
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
                            filter: true,
                            widthMob: '215',
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
                            width: '400',
                            required: true,
                            filter: true,
                            screenWidth: '576',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'model',
                    label: 'Модель',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '200',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'icon',
                    label: 'Иконка',
                    class: FieldString::class,
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

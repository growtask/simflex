<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class AdminMenu extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'admin_menu',
            orderBy: 'npp',
            fields: [
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
                            fkTable: 'admin_menu',
                            fkKey: 'menu_id',
                            fkLabel: 'name',
                            fkIsPid: true,
                            fk: 'admin_menu.menu_id.name',
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
                            defaultValue: '0',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'priv_id',
                    class: FieldInt::class,
                    label: 'Привилегия',
                    params: [
                        'main' => new FieldParams(
                            width: '120',
                            required: true,
                            filter: true,
                            isFk: true,
                            fkTable: 'user_priv',
                            fkKey: 'priv_id',
                            fkLabel: 'name',
                            fk: 'user_priv.priv_id.name',
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
                    class: FieldString::class,
                    label: 'Модель',
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

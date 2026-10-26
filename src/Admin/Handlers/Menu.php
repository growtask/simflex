<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldComponentSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\NppField;
use Simflex\Admin\Table\Presets\ParentField;
use Simflex\Admin\Table\Table;
use Simflex\Core\Models\Menu as MenuModel;

class Menu extends Table
{
    protected static string $model = MenuModel::class;
    protected static ?string $defaultOrderBy = 'npp';

    public static function getFields(): array
    {
        return [
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
                width: '104',
                filter: true,
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
                e2n: true,
                filter: true,
                widthMob: '200',
            ),
            new FieldDefinition(
                name: 'name',
                class: FieldString::class,
                label: 'Название',
                width: '1',
                required: true,
                filter: true,
                screenWidth: '991',
                widthMob: '200',
            ),
            new FieldDefinition(
                name: 'link',
                class: FieldString::class,
                label: 'Ссылка',
                width: '300',
                filter: true,
            ),
        ];
    }
}

<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\NppField;
use Simflex\Admin\Table\Table;
use Simflex\Core\Models\ModelSettings;

class Settings extends Table
{
    protected static string $model = ModelSettings::class;
    protected static ?string $defaultOrderBy = 'npp';

    public static function getFields(): array
    {
        return [
            new FieldDefinition(
                name: 'alias',
                class: FieldString::class,
                label: 'Алиас',
                width: '200',
                required: true,
                filter: true,
            ),
            new FieldDefinition(
                name: 'name',
                class: FieldString::class,
                label: 'Наименование',
                width: '1',
                required: true,
                filter: true,
                widthMob: '200',
            ),
            new NppField(),
            new IdField(
                name: 'setting_id',
                width: '54',
                filter: true,
                widthMob: '40',
            ),
            new FieldDefinition(
                name: 'value',
                class: FieldText::class,
                label: 'Значение',
                width: '200',
            ),
            new FieldDefinition(
                name: 'type',
                class: FieldEnum::class,
                label: 'Тип',
                defaultValue: 'string',
                readonly: true,
            ),
            new FieldDefinition(
                name: 'enum_values',
                class: FieldString::class,
                label: 'Значения enum',
                e2n: true,
                hidden: true,
            ),
        ];
    }
}

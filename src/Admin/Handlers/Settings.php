<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldEnum;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Fields\FieldText;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Presets\NppField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Models\Settings as ModelSettings;

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

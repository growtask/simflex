<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldDateTime;
use Simflex\Core\Admin\Fields\FieldEnum;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Models\Log as LogModel;

class Log extends Table
{
    protected static string $model = LogModel::class;
    protected static ?string $defaultOrderBy = 'log_id';
    protected static bool $defaultOrderDesc = true;

    public static function getFields(): array
    {
        return [
            new IdField(name: 'log_id'),
            new FieldDefinition(
                name: 'datetime',
                class: FieldDateTime::class,
                label: 'Время',
                width: '150',
            ),
            new FieldDefinition(
                name: 'action',
                class: FieldEnum::class,
                label: 'Действие',
                width: '180',
                filter: true,
            ),
            new FieldDefinition(
                name: 'ip',
                class: FieldString::class,
                label: 'IP адрес',
                width: '180',
            ),
            new FieldDefinition(
                name: 'browser',
                class: FieldString::class,
                label: 'Браузер',
                width: '250',
            ),
            new FieldDefinition(
                name: 'data',
                class: FieldString::class,
                label: 'Информация',
                width: '1',
            ),
        ];
    }
}

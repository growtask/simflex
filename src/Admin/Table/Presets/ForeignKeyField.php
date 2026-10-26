<?php

namespace Simflex\Admin\Table\Presets;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Table\FieldDefinition;

/**
 * Ссылка на запись другой таблицы, выбирается из списка её записей.
 * Необязательная ссылка может быть пустой (NULL)
 */
readonly class ForeignKeyField extends FieldDefinition
{
    /**
     * @param string $table Таблица, на которую ссылается поле
     * @param string|null $key Её первичный ключ, если не задан — как $name
     * @param string $display Её колонка, показываемая вместо id
     */
    public function __construct(
        string $name,
        string $label,
        string $table,
        ?string $key = null,
        string $display = 'name',
        string $help = '',
        int|string $width = 0,
        bool $required = false,
        bool $filter = true,
        int|string $widthMob = 0,
    ) {
        parent::__construct(
            name: $name,
            class: FieldInt::class,
            label: $label,
            help: $help,
            e2n: !$required,
            width: $width,
            required: $required,
            filter: $filter,
            widthMob: $widthMob,
            isFk: true,
            fkTable: $table,
            fkKey: $key ?? $name,
            fkLabel: $display,
        );
    }
}

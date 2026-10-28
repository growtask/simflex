<?php

namespace Simflex\Core\Admin\Table\Presets;

use Simflex\Core\Admin\Fields\FieldInt;
use Simflex\Core\Admin\Table\FieldDefinition;

/**
 * Родительская запись той же таблицы: делает таблицу деревом. Пусто (NULL) у корневых записей
 */
readonly class ParentField extends FieldDefinition
{
    /**
     * @param string $table Сама таблица
     * @param string $key Её первичный ключ
     * @param string $display Её колонка, показываемая вместо id
     */
    public function __construct(
        string $name,
        string $table,
        string $key,
        string $display = 'name',
        string $label = 'Родитель',
        bool $filter = false,
    ) {
        parent::__construct(
            name: $name,
            class: FieldInt::class,
            label: $label,
            e2n: true,
            filter: $filter,
            isFk: true,
            fkTable: $table,
            fkKey: $key,
            fkLabel: $display,
            fkIsPid: true,
        );
    }
}

<?php

namespace Simflex\Admin\Table\Presets;

use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Table\FieldDefinition;

/**
 * Связи «многие ко многим» (FieldMultiKey): записи другой таблицы, выбранные тегами. Колонки нет, связи хранятся
 * в таблице связей: id записи раздела + ключ выбранной записи
 */
readonly class MultiKeyField extends FieldDefinition
{
    /**
     * @param string $values Таблица выбираемых записей
     * @param string $relations Таблица связей
     * @param string $key Первичный ключ таблицы $values, он же колонка таблицы связей с выбранной записью
     * @param string $display Колонка таблицы $values, которая показывается вместо ключа
     * @param string $relationKey Колонка таблицы связей с id записи раздела. Пусто — как первичный ключ раздела
     * @param string $parentKey Колонка родителя в таблице $values: запись подписывается «Имя (Родитель)»
     */
    public function __construct(
        string $name,
        string $label,
        string $values,
        string $relations,
        string $key,
        string $display = 'name',
        string $relationKey = '',
        string $parentKey = '',
        string $help = '',
        int|string $width = 0,
        bool $filter = false,
        string $pos = '',
        string $posGroup = '',
    ) {
        parent::__construct(
            name: $name,
            class: FieldMultiKey::class,
            label: $label,
            help: $help,
            width: $width,
            filter: $filter,
            pos: $pos,
            posGroup: $posGroup,
            params: [
                'table_values' => $values,
                'table_relations' => $relations,
                'key' => $key,
                'key_alias' => $display,
                'relation_key' => $relationKey,
                'parent_key' => $parentKey,
            ],
        );
    }
}

<?php

namespace Simflex\Core\System;

use Simflex\Core\Models\Settings as ModelSettings;

/**
 * Настройки сайта: таблица settings, раздел админки «Настройки». Загружаются один раз за запрос
 */
class Settings
{
    /** @var array<string, mixed>|null alias => значение, null - ещё не загружены */
    protected static ?array $values = null;

    /**
     * Значение настройки по алиасу.
     *
     * Подставляет значения в строку: для `greeting` = 'Hello {name}' `get('greeting', '', ['name' => 'John'])`
     * вернёт 'Hello John'. Если настройки нет, она создаётся в таблице settings со значением $defaultValue
     * (скаляр или null) и становится доступной для редактирования в админке
     *
     * @param mixed $defaultValue Значение, если настройки нет
     * @param array<string, scalar> $interp Значения для подстановки {ключ}
     */
    public static function get(string $key, mixed $defaultValue = null, array $interp = []): mixed
    {
        $values = static::getAll();
        if (!array_key_exists($key, $values)) {
            static::$values[$key] = $defaultValue;
            if ($key !== '' && (is_scalar($defaultValue) || $defaultValue === null)) {
                static::create($key, $defaultValue);
            }
        }

        $value = static::$values[$key] ?? $defaultValue;
        if (is_string($value) && $interp) {
            $value = str_replace(
                array_map(fn($k) => '{' . $k . '}', array_keys($interp)),
                array_values($interp),
                $value,
            );
        }

        return $value;
    }

    /**
     * @return array<string, mixed> Все настройки: alias => значение
     */
    public static function getAll(): array
    {
        return static::$values ??= ModelSettings::values();
    }

    /**
     * Перечитать настройки из базы при следующем обращении
     */
    public static function reset(): void
    {
        static::$values = null;
    }

    protected static function create(string $key, mixed $defaultValue): void
    {
        $type = match (true) {
            is_int($defaultValue) => 'int',
            is_bool($defaultValue) => 'bool',
            default => 'string',
        };
        ModelSettings::insert([
            'name' => $key,
            'alias' => $key,
            'value' => is_bool($defaultValue) ? (int)$defaultValue : (string)$defaultValue,
            'type' => $type,
            'enum_values' => '',
        ]);
    }
}

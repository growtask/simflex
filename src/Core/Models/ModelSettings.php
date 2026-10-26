<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $setting_id
 * @property int $npp
 * @property string $name
 * @property string $alias
 * @property string $value
 * @property string $type
 * @property string $enum_values
 */
class ModelSettings extends ModelBase
{
    protected static string $table = 'settings';
    protected static string $primaryKeyName = 'setting_id';

    public static function get(string $key): mixed
    {
        return static::findOne(['alias' => $key])['value'] ?? null;
    }

    /**
     * @return array<string, string> alias => value всех настроек
     */
    public static function values(): array
    {
        return array_map(
            fn(array $row) => $row['value'],
            static::findAdv()->select('alias, value')->asArray()->all('alias')
        );
    }
}

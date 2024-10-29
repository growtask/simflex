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
    protected static $table = 'settings';
    protected static $primaryKeyName = 'setting_id';

    public static function get(string $key)
    {
        return static::findOne(['alias' => $key])['value'] ?? null;
    }
}
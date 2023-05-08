<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

class ModelSettings extends ModelBase
{
    protected static $table = 'settings';
    protected static $primaryKeyName = 'setting_id';

    public static function get(string $key)
    {
        return static::findOne(['alias' => $key])['value'] ?? null;
    }
}
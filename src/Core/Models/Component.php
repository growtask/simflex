<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $component_id
 * @property string $class
 * @property string $name
 * @property string $params
 */
class Component extends ModelBase
{
    protected static $primaryKeyName = 'component_id';
    protected static $table = 'component';

    public static function byClass(string $class): ?static
    {
        return static::findOne(['class' => $class]);
    }
}
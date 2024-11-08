<?php
namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\ModelBase;

/**
 * Class StructField
 *
 * @property int $field_id
 * @property string $name
 * @property string $class
 */
class StructField extends ModelBase
{
    protected static $table = 'struct_field';
    protected static $primaryKeyName = 'field_id';

    /**
     * @throws Exception
     */
    public static function byClass(string $class): ?static
    {
        return static::findOne(['class' => $class]);
    }
}
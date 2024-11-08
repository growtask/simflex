<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Class StructFieldParam
 *
 * @property int $fp_id
 * @property int $field_id
 * @property string $name
 * @property string $label
 * @property int $type_id
 * @property string $help
 * @property string $default_value
 */
class StructFieldParam extends ModelBase
{
    protected static $table = 'struct_field_param';
    protected static $primaryKeyName = 'fp_id';
}
<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Class StructTable
 *
 * @property int $table_id
 * @property string $name
 * @property string $order_by
 * @property bool $order_desc
 * @property int $priv_add
 * @property int $priv_edit
 * @property int $priv_delete
 * @property string $class
 */
class StructTable extends ModelBase
{
    protected static $table = 'struct_table';
    protected static $primaryKeyName = 'table_id';
}
<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int module_id
 * @property string class
 * @property string name
 * @property string type
 * @property boolean postexec
 */
class Module extends ModelBase
{
    protected static $table = 'module';
    protected static $primaryKeyName = 'module_id';
}
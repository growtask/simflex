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
    protected static string $table = 'module';
    protected static string $primaryKeyName = 'module_id';
}

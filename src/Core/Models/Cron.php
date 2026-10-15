<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int id
 * @property bool active
 * @property string timing
 * @property string name
 * @property int ext_id
 * @property int module_id
 * @property string plugin_name
 * @property string action
 * @property string cparams
 * @property string class_fqn
 */
class Cron extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'cron';
}

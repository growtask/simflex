<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int id
 * @property int cron_id
 * @property string datetime
 * @property string result
 */
class CronLog extends ModelBase
{
    protected static $table = 'cron_log';
    protected static $primaryKeyName = 'id';
}
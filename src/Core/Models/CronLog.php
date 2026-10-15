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
    protected static string $table = 'cron_log';
    protected static string $primaryKeyName = 'id';
}

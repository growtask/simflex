<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int id
 * @property string job Job key in provider/cron.php
 * @property string datetime
 * @property string result
 */
class CronLog extends ModelBase
{
    protected static string $table = 'cron_log';
    protected static string $primaryKeyName = 'id';

    /**
     * Cron job: deletes the log records older than $months
     */
    public static function deleteOld(int $months = 2): void
    {
        static::bulkDelete("adddate(datetime, interval $months month) < now()");
    }
}

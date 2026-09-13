<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Admin panel settings of a cron job declared in provider/cron.php, see \Simflex\Core\Cron\CronRegistry
 * @property int id
 * @property string job Job key in provider/cron.php
 * @property bool active
 * @property string timing
 */
class Cron extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'cron';
}

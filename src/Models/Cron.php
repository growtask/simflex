<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Настройки в админке задачи cron, объявленной в provider/cron.php, см. \Simflex\Core\Cron\CronRegistry
 * @property int id
 * @property string job Ключ задачи в provider/cron.php
 * @property bool active
 * @property string timing
 */
class Cron extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'cron';
}

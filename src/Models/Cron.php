<?php

namespace Simflex\Models;

use Simflex\Core\ModelBase;

/**
 * Настройки в админке задачи cron, объявленной в provider/cron.php, см. \Simflex\Cron\CronRegistry
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

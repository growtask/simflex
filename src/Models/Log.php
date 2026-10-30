<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * @property int $log_id
 * @property string $datetime
 * @property string $action login_attempt|login_success
 * @property string $ip
 * @property string $browser
 * @property string $data
 */
class Log extends ModelBase
{
    protected static string $table = 'log';

    public const ?string COLUMN_TIME_CREATE = null;
    public const ?string COLUMN_TIME_UPDATE = null;
    protected static string $primaryKeyName = 'log_id';
}

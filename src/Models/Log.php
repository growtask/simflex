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
    protected static string $primaryKeyName = 'log_id';
}

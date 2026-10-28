<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Сервис отправки sms: alias - класс драйвера Simflex\Core\Sms\Driver<Alias>, остальные колонки - его настройки
 *
 * @property int $driver_id
 * @property string $alias
 * @property bool $active
 */
class SmsDriver extends ModelBase
{
    protected static string $table = 'sms_driver';
    protected static string $primaryKeyName = 'driver_id';
}

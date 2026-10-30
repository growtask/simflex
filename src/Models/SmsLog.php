<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Отправленное sms
 *
 * @property int $sms_id
 * @property string $phone
 * @property string $text
 * @property string $sender
 * @property string $extra
 * @property string $state Состояние: DriverBase::STATE_*
 * @property int|null $driver_id
 * @property string|null $driver_sms_id Id sms у сервиса отправки
 * @property float|null $price
 * @property int|null $error_code
 * @property string|null $error_text
 * @property string $time_create
 * @property string|null $time_send
 * @property string|null $time_delivery
 */
class SmsLog extends ModelBase
{
    protected static string $table = 'sms_log';

    public const ?string COLUMN_TIME_UPDATE = null;
    protected static string $primaryKeyName = 'sms_id';
}

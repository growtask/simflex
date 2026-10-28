<?php

namespace Simflex\Core\Sms;

/**
 * @todo Incomplete: нет миграций для таблиц sms_log, sms_driver, поэтому в реальном окружении код падает
 *       на SQL. Будет вынесено в отдельный composer-пакет.
 */
class DriverFake extends DriverBase
{
    public function send($phone, $text, $from, $smsId = 0): array|bool
    {
        return ['success' => true, 'driver_sms_id' => 'fake-' . $smsId, 'price' => 0, 'error_text' => '', 'error_code' => 0];
    }
}

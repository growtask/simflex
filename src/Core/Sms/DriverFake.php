<?php

namespace Simflex\Core\Sms;

/**
 * @todo Incomplete: there are no migrations for the sms_log, sms_driver tables, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class DriverFake extends DriverBase
{
    public function send($phone, $text, $from, $smsId = 0): array|bool
    {
        return ['success' => true, 'driver_sms_id' => 'fake-' . $smsId, 'price' => 0, 'error_text' => '', 'error_code' => 0];
    }
}

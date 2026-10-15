<?php


namespace Simflex\Core\Sms;


use Simflex\Core\Sms\DriverBase;

class DriverFake extends DriverBase
{

    public function send($phone, $text, $from, $smsId = 0): array|bool
    {
        return ['success' => true, 'driver_sms_id' => 'fake-' . $smsId, 'price' => 0, 'error_text' => '', 'error_code' => 0];
    }

}
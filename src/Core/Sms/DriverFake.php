<?php


namespace Simflex\Core\Sms;


use Simflex\Core\Sms\DriverBase;

class DriverFake extends DriverBase
{

    public function send($phone, $text, $from, $smsId = 0)
    {
        return true;
    }

}
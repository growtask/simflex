<?php

namespace Simflex\Core\Sms;

use Simflex\Core\DB;
use Simflex\Core\Time;

/**
 * @todo Incomplete: there are no migrations for the sms_log, sms_driver tables, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
abstract class DriverBase
{
    public const ERROR_NO_MONEY = 1;

    public const STATE_NEW = 'new';
    public const STATE_SEND = 'send';
    public const STATE_DELIVERY = 'delivery';
    public const STATE_ERROR = 'error';

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Отправка СМС
     * @param string $phone
     * @param string $text
     * @param string $from
     * @return array  bool success, mixed driver_sms_id, float price, string error_text, int error_code
     */
    abstract public function send($phone, $text, $from, $smsId = 0): array|bool;

    /**
     * Получение статуса СМС
     * @param mixed $driverSmsId Идентификатор СМС в системе сервиса СМС-отправки
     * @return array string state, string error_text, time
     */
    public function getState($driverSmsId): array
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    /**
     * Чтение входящих сообщений о доставке
     */
    public function readDeliveries(): void
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    /**
     * @param string $driverSmsId
     * @param array $result [string state, mixed time, error_code, error_text]
     */
    protected function updateState($driverSmsId, array $result): void
    {
        $set = ["state = '{$result['state']}'"];
        if (self::STATE_DELIVERY == $result['state']) {
            $time = Time::mysql($result['time']);
            $set[] = "time_delivery = '$time'";
        }
        if (self::STATE_ERROR == $result['state']) {
            $set[] = 'error_code = ' . (int)$result['error_code'];
            $set[] = "error_text = '{$result['error_text']}'";
        }
        $q = '
            UPDATE sms_log SET ' . implode(', ', $set) . " 
            WHERE driver_sms_id = '$driverSmsId'
            AND driver_id = {$this->config['driver_id']}
        ";
        DB::query($q);
    }
}

<?php

namespace Simflex\Sms;

use Simflex\DB\DB;
use Simflex\Helpers\Time;

/**
 * @todo Incomplete: нет миграций для таблиц sms_log, sms_driver, поэтому в реальном окружении код падает
 *       на SQL. Будет вынесено в отдельный composer-пакет.
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
     * @return array  bool success - успешно, mixed driver_sms_id, float price - цена, string error_text, int error_code
     */
    abstract public function send($phone, $text, $from, $smsId = 0): array|bool;

    /**
     * Получение статуса СМС
     * @param mixed $driverSmsId Идентификатор СМС в системе сервиса СМС-отправки
     * @return array string state - состояние, string error_text, time - время
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
     * @param array $result [string state, mixed time, error_code, error_text] - состояние, время, код и текст ошибки
     */
    protected function updateState($driverSmsId, array $result): void
    {
        $set = ["state = '{$result['state']}'"];
        if (self::STATE_DELIVERY === $result['state']) {
            $time = Time::mysql($result['time']);
            $set[] = "time_delivery = '$time'";
        }
        if (self::STATE_ERROR === $result['state']) {
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

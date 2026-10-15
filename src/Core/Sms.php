<?php

namespace Simflex\Core;

use Simflex\Core\DB;
use Simflex\Core\Helpers\Phone;
use Simflex\Core\Core;
use Simflex\Core\Sms\DriverBase;


/**
 * @todo Incomplete: there are no migrations for the sms_log, sms_driver tables, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class Sms
{

    protected static ?DriverBase $driver = null;

    /**
     * @param string $from [optional = ''] Если '', берем значение из настроек сайта: sms_sender
     * @param string $extra [optional = ''] - например номер заказа, чтобы потом можно было отследить
     * @param int $lastSendTimeout [optional = 0] Лимит времени в секундах после последней отправки смс на этот номер
     * @return boolean|string отправлено или нет, если сервис вернул ошибку, функция вернет эту ошибку в виде строки
     */
    public static function send($phone, string $text, string $from = '', $extra = '', int $lastSendTimeout = 0): string|bool
    {
        if (!$from) {
            $from = Core::siteParam('sms_sender');
        }

        $phone = Phone::extract($phone);
        if (!$phone) {
            return 'Not correct phone';
        }

        if ($lastSendTimeout) {
            $q = "
                SELECT * FROM sms_log WHERE phone = $phone 
                AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time_create)) < $lastSendTimeout LIMIT 1
            ";
            $exist = DB::result($q);
            if ($exist) {
                return 'Last send too recently';
            }
        }

        $q = "
            INSERT INTO sms_log
            SET phone = $phone, text = '" . DB::escape($text) . "', sender = '" . DB::escape($from) . "', 
            extra = '" . DB::escape($extra) . "', time_create = now()
        ";
        $success = DB::query($q);
        if (!$success) {
            return 'Server error';
        }
        $smsId = DB::insertId();

        $result = self::sendById($smsId, $phone, $text, $from);
        return $result;
    }

    /**
     * @return boolean|string отправлено или нет, если сервис вернул ошибку, функция вернет эту ошибку в виде строки
     */
    private static function sendById($smsId, $phone = false, $text = false, $from = false): string|bool
    {
        $smsId = (int)$smsId;
        if (!$smsId) {
            return 'no sms id';
        }
        if ($phone === false) {
            $q = "SELECT * FROM sms_log WHERE sms_id = $smsId";
            $row = DB::result($q);
            $phone = $row['phone'];
            $text = $row['text'];
            $from = $row['sender'];
        }

        $driverId = null;
        if (empty(Core::siteParam('send_deny_sms'))) {
            $driverInstance = self::getDriver();
            if (empty($driverInstance)) {
                return false;
            }
            $driverId = (int)$driverInstance->getConfig()['driver_id'];
            $sendResult = $driverInstance->send($phone, $text, $from, $smsId);
        } else {
            $sendResult = ['success' => true, 'driver_sms_id' => '999', 'price' => 1, 'error_text' => '', 'error_code' => 0];
        }

        $set = ["time_send = now(), state = '" . ($sendResult['success'] ? 'send' : 'error') . "'"];
        $set[] = "driver_id = " . ($driverId ?? 'NULL');
        if (!empty($sendResult['error_text'])) {
            $set[] = "error_text = '" . DB::escape($sendResult['error_text']) . "'";
            $set[] = "error_code = " . (int)$sendResult['error_code'];
        } else {
            $set[] = "driver_sms_id = '{$sendResult['driver_sms_id']}', price = {$sendResult['price']}";
        }
        $q = "UPDATE sms_log set " . implode(', ', $set) . " WHERE sms_id = $smsId";
        DB::query($q);
        return !empty($sendResult['error_text']) ? $sendResult['error_text'] : true;
    }

    public static function getDriver(): ?DriverBase
    {
        if (empty(static::$driver)) {
            $q = "SELECT * FROM sms_driver WHERE active = 1 LIMIT 1";
            $driverInfo = DB::result($q);
            static::setDriverByInfo($driverInfo);
        }
        return static::$driver;
    }

    /**
     * @param array $driverInfo data from sms_driver
     */
    protected static function setDriverByInfo($driverInfo): void
    {
        static::$driver = static::createDriverByInfo($driverInfo);
    }

    /**
     * @param array $driverInfo data from sms_driver
     */
    protected static function createDriverByInfo($driverInfo): ?DriverBase
    {
        if (!isset($driverInfo['driver_id'])) {
            return null;
        }
        // драйверы лежат в Core/Sms/: alias "smpp" => Simflex\Core\Sms\DriverSmpp
        $driverClass = 'Simflex\\Core\\Sms\\Driver' . ucfirst((string)$driverInfo['alias']);
        if (!class_exists($driverClass) || !is_subclass_of($driverClass, DriverBase::class)) {
            Log::error('Sms: driver class {class} not found for alias "{alias}"', [
                'class' => $driverClass,
                'alias' => $driverInfo['alias'],
            ]);
            return null;
        }
        return new $driverClass($driverInfo);
    }

    public static function setDriver(string $driverAlias): void
    {
        $driverAlias = DB::escape($driverAlias);
        $q = "SELECT * FROM sms_driver WHERE alias = '$driverAlias' LIMIT 1";
        $driverInfo = DB::result($q);
        static::setDriverByInfo($driverInfo);
    }

    public static function resend($smsId): string|bool
    {
        $result = self::sendById($smsId);
        return $result;
    }

    public static function renewStates(): void
    {
        $q = "SELECT * FROM sms_log WHERE state = 'send' AND driver_id IS NOT NULL AND driver_sms_id IS NOT NULL LIMIT 100";
        $rows = DB::assoc($q) ?: [];
        $q = "SELECT * FROM sms_driver WHERE active = 1";
        $drivers = DB::assoc($q, 'driver_id') ?: [];
        foreach ($rows as $row) {
            if ($driver = $drivers[$row['driver_id']] ?? null) {
                self::renewState($row, $driver);
            } else {
                $set = ["state = '" . DriverBase::STATE_ERROR . "'"];
                $set[] = "time_delivery = NOW()";
                $set[] = "error_text = 'Driver disabled'";
                $set[] = "error_code = 403";
                $q = "UPDATE sms_log SET " . implode(', ', $set) . " WHERE sms_id = {$row['sms_id']}";
                DB::query($q);
            }
        }
        foreach ($drivers as $driver) {
            $driverInstance = self::createDriverByInfo($driver);
            if (!$driverInstance) {
                continue;
            }
            try {
                $driverInstance->readDeliveries();
            } catch (\BadMethodCallException $e) {

            }
        }
    }

    private static function renewState($sms, $driverInfo): void
    {
        $driverInstance = self::createDriverByInfo($driverInfo);
        if (!$driverInstance) {
            return;
        }
        try {
            $result = $driverInstance->getState($sms['driver_sms_id']);
        } catch (\BadMethodCallException $e) {
            return;
        }
        $set = ["state = '{$result['state']}'"];
        if (DriverBase::STATE_DELIVERY == $result['state']) {
            $set[] = "time_delivery = '{$result['time']}'";
        }
        if (DriverBase::STATE_ERROR == $result['state']) {
            $set[] = "error_text = '{$result['error_text']}'";
        }
        $q = "UPDATE sms_log SET " . implode(', ', $set) . " WHERE sms_id = {$sms['sms_id']}";
        DB::query($q);
    }

    /**
     * @return bool true - деньги кончились, false - все ок
     */
    public static function checkBalance(): bool
    {
        $q = "SELECT error_code FROM sms_log ORDER BY sms_id DESC LIMIT 1";
        $lastErrorCode = (int)DB::result($q, 0);
        return DriverBase::ERROR_NO_MONEY === $lastErrorCode;
    }

}

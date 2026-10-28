<?php

namespace Simflex\Core\Sms;

use Simflex\Core\DB\Expr;
use Simflex\Core\Helpers\Phone;
use Simflex\Core\Models\SmsDriver;
use Simflex\Core\Models\SmsLog;
use Simflex\Core\Logger\Log;
use Simflex\Core\System\Settings;

/**
 * @todo Incomplete: нет миграций для таблиц sms_log, sms_driver, поэтому в реальном окружении код падает
 *       на SQL. Будет вынесено в отдельный composer-пакет.
 */
class Sms
{
    protected static ?DriverBase $driver = null;

    /**
     * @param string $from [по умолчанию ''] Если '', берем значение из настроек сайта: sms_sender
     * @param string $extra [по умолчанию ''] - например номер заказа, чтобы потом можно было отследить
     * @param int $lastSendTimeout [по умолчанию 0] Лимит времени в секундах после последней отправки смс на этот номер
     * @return boolean|string отправлено или нет, если сервис вернул ошибку, функция вернет эту ошибку в виде строки
     */
    public static function send($phone, string $text, string $from = '', $extra = '', int $lastSendTimeout = 0): string|bool
    {
        if (!$from) {
            $from = Settings::get('sms_sender');
        }

        $phone = Phone::extract($phone);
        if (!$phone) {
            return 'Not correct phone';
        }

        if ($lastSendTimeout) {
            $exist = SmsLog::findAdv()
                ->where(['phone' => $phone])
                ->andWhere('UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(time_create) < :timeout')
                ->bind(['timeout' => $lastSendTimeout])
                ->fetchOne();
            if ($exist) {
                return 'Last send too recently';
            }
        }

        $sms = SmsLog::insertStatic([
            'phone' => $phone,
            'text' => $text,
            'sender' => $from,
            'extra' => (string)$extra,
            'time_create' => new Expr('NOW()'),
        ]);
        if (!$sms) {
            return 'Server error';
        }

        return self::sendById($sms->getId(), $phone, $text, $from);
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
            $row = SmsLog::findOne(['sms_id' => $smsId]);
            if (!$row) {
                return 'no sms';
            }
            $phone = $row['phone'];
            $text = $row['text'];
            $from = $row['sender'];
        }

        $driverId = null;
        if (empty(Settings::get('send_deny_sms'))) {
            $driverInstance = self::getDriver();
            if (empty($driverInstance)) {
                return false;
            }
            $driverId = (int)$driverInstance->getConfig()['driver_id'];
            $sendResult = $driverInstance->send($phone, $text, $from, $smsId);
        } else {
            $sendResult = ['success' => true, 'driver_sms_id' => '999', 'price' => 1, 'error_text' => '', 'error_code' => 0];
        }

        $set = [
            'time_send' => new Expr('NOW()'),
            'state' => $sendResult['success'] ? 'send' : 'error',
            'driver_id' => $driverId,
        ];
        if (!empty($sendResult['error_text'])) {
            $set['error_text'] = $sendResult['error_text'];
            $set['error_code'] = (int)$sendResult['error_code'];
        } else {
            $set['driver_sms_id'] = (string)$sendResult['driver_sms_id'];
            $set['price'] = $sendResult['price'];
        }
        SmsLog::bulkUpdate($set, ['sms_id' => $smsId]);
        return !empty($sendResult['error_text']) ? $sendResult['error_text'] : true;
    }

    public static function getDriver(): ?DriverBase
    {
        if (empty(static::$driver)) {
            static::setDriverByInfo(SmsDriver::findOne(['active' => 1])?->toArray() ?? []);
        }
        return static::$driver;
    }

    /**
     * @param array $driverInfo данные из sms_driver
     */
    protected static function setDriverByInfo($driverInfo): void
    {
        static::$driver = static::createDriverByInfo($driverInfo);
    }

    /**
     * @param array $driverInfo данные из sms_driver
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
        static::setDriverByInfo(SmsDriver::findOne(['alias' => $driverAlias])?->toArray() ?? []);
    }

    public static function resend($smsId): string|bool
    {
        $result = self::sendById($smsId);
        return $result;
    }

    public static function renewStates(): void
    {
        $rows = SmsLog::findAdv()
            ->asArray()
            ->where(['state' => 'send'])
            ->andWhere('driver_id IS NOT NULL AND driver_sms_id IS NOT NULL')
            ->limit(100)
            ->all();
        $drivers = SmsDriver::findAdv()->asArray()->where(['active' => 1])->all('driver_id');
        foreach ($rows as $row) {
            if ($driver = $drivers[$row['driver_id']] ?? null) {
                self::renewState($row, $driver);
            } else {
                SmsLog::bulkUpdate([
                    'state' => DriverBase::STATE_ERROR,
                    'time_delivery' => new Expr('NOW()'),
                    'error_text' => 'Driver disabled',
                    'error_code' => 403,
                ], ['sms_id' => $row['sms_id']]);
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
        $set = ['state' => $result['state']];
        if (DriverBase::STATE_DELIVERY === $result['state']) {
            $set['time_delivery'] = $result['time'];
        }
        if (DriverBase::STATE_ERROR === $result['state']) {
            $set['error_text'] = $result['error_text'];
        }
        SmsLog::bulkUpdate($set, ['sms_id' => $sms['sms_id']]);
    }

    /**
     * @return bool true - деньги кончились, false - все ок
     */
    public static function checkBalance(): bool
    {
        $lastErrorCode = (int)SmsLog::findAdv()->select('error_code')->orderBy('sms_id DESC')->limit(1)->fetchScalar();
        return DriverBase::ERROR_NO_MONEY === $lastErrorCode;
    }
}

<?php

namespace Simflex\Sms;

use PhpSmpp\Service\Sender as SmppSender;
use PhpSmpp\SMPP\SMPP;
use PhpSmpp\SMPP\Unit\Sm;
use Simflex\Helpers\Phone;
use Simflex\Logger\Log;

/**
 * Класс DriverSmpp
 * @require https://github.com/glushkovds/php-smpp
 *
 * @todo Incomplete: нет миграций для таблиц sms_log, sms_driver, поэтому в реальном окружении код падает
 *       на SQL. Будет вынесено в отдельный composer-пакет.
 */
class DriverSmpp extends DriverBase
{
    public const STATES = [
        SMPP::STATE_ENROUTE => self::STATE_SEND,
        SMPP::STATE_DELIVERED => self::STATE_DELIVERY,
        SMPP::STATE_EXPIRED => self::STATE_ERROR,
        SMPP::STATE_DELETED => self::STATE_ERROR,
        SMPP::STATE_UNDELIVERABLE => self::STATE_ERROR,
        SMPP::STATE_ACCEPTED => self::STATE_SEND,
        SMPP::STATE_UNKNOWN => self::STATE_SEND,
        SMPP::STATE_REJECTED => self::STATE_ERROR,
    ];

    /**
     * @param string $phone
     * @param string $text
     * @param string $from
     */
    public function send($phone, $text, $from, $smsId = 0): array|bool
    {
        try {
            $error = '';
            $errorCode = 0;
            $phone = Phone::extract7($phone);
            $service = new SmppSender([$this->config['url']], $this->config['login'], $this->config['pass'], false);
            $service->client->csmsMethod = \PhpSmpp\SMPP\SmppClient::CSMS_8BIT_UDH;
            $driverSmsId = $service->send($phone, $text, (string)$from);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode() ?: 500;
            $driverSmsId = 0;
        }

        if (!empty($service)) {
            try {
                $service->client->close();
            } catch (\Throwable $e) {

            }
        }

        return [
            'success' => !empty($driverSmsId),
            'driver_sms_id' => $driverSmsId,
            'price' => 1,
            'error_code' => $errorCode,
            'error_text' => $error,
        ];
    }

    public function readDeliveries(): void
    {
        $service = new \PhpSmpp\Service\Listener([$this->config['url']], $this->config['login'], $this->config['pass'], true);
        $service->listenOnce(function (Sm $sm) {
            if (!($sm instanceof \PhpSmpp\SMPP\Unit\DeliverReceiptSm)) {
                Log::debug('SMPP: skipped a unit that is not a delivery receipt');
                return;
            }

            $state = static::STATES[$sm->state] ?? static::STATE_ERROR;
            $this->updateState($sm->msgId, [
                'state' => $state,
                'time' => $sm->doneDate,
                'error_code' => $sm->receiptErrorCode,
                'error_text' => $sm->receiptErrorText,
            ]);
        });
    }
}

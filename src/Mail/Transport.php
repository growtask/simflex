<?php

namespace Simflex\Mail;

/**
 * Интерфейс почтового транспорта
 */
interface Transport
{
    /**
     * Отправляет письмо
     * @param Mail $mail Письмо
     * @return bool true, если отправлено успешно
     */
    public function send(Mail $mail): bool;
}

<?php

namespace Simflex\Core\Mail;

use Simflex\Core\DI\Service;

/**
 * Сервис отправки почты
 */
class Mailer implements Service
{
    /**
     * @var Transport Почтовый транспорт
     */
    public Transport $transport;

    public static function getServiceName(): string
    {
        return 'mailer';
    }

    /**
     * Отправляет письмо
     * @param string $subject Тема
     * @param string $to Адрес получателя
     * @param callable $fn Колбэк настройки письма. Получает единственный объект типа Mail
     * @return bool true, если отправлено успешно
     */
    public function send(string $subject, string $to, callable $fn): bool
    {
        // проверяем, что транспорт задан
        if (!$this->transport) {
            throw new \AssertionError('Transport is not set');
        }

        // настраиваем письмо
        $mail = new Mail();
        $mail->subject = $subject;
        $mail->to = $to;
        $fn($mail);

        // отправляем
        return $this->transport->send($mail);
    }

    /**
     * Отправляет письмо с содержимым из шаблона
     * @param string $subject Тема
     * @param string $to Адрес получателя
     * @param string $template Шаблон
     * @param array $data Произвольные данные для шаблона
     * @param bool $strip Вырезать ли теги из альтернативного тела
     * @return bool true, если отправлено успешно
     */
    public function sendTemplate(string $subject, string $to, string $template, array $data = [], bool $strip = true): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($template, $data, $strip) {
            $mail->setBodyFromTemplate($template, $data, $strip);
        });
    }

    /**
     * Отправляет письмо с текстовым содержимым
     * @param string $subject Тема
     * @param string $to Адрес получателя
     * @param string $body Тело письма
     * @param string|null $altBody Альтернативное тело (если null, используется $body)
     * @return bool true, если отправлено успешно
     */
    public function sendText(string $subject, string $to, string $body, ?string $altBody = null): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($body, $altBody) {
            $mail->body = $body;
            $mail->altBody = $altBody ?? $body;
        });
    }
}

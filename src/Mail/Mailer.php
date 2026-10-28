<?php

namespace Simflex\Core\Mail;

use Simflex\Core\System\Container;
use Simflex\Core\System\DI\Service;
use Simflex\Core\Mail\Transports\PhpMailer;

/**
 * Сервис отправки почты (имя в контейнере - mailer). Транспорт - класс из конфига mail.transport, по умолчанию
 * SMTP через PHPMailer (Transport\PhpMailer)
 */
class Mailer implements Service
{
    public Transport $transport;

    public static function getServiceName(): string
    {
        return 'mailer';
    }

    public function __construct(?Transport $transport = null)
    {
        $class = Container::isSet('config') ? (Container::getConfig()->mail['transport'] ?? null) : null;
        $this->transport = $transport ?? ($class ? new $class() : new PhpMailer());
    }

    /**
     * Сервис из контейнера, если он объявлен в provider/services.php, иначе - новый с транспортом из конфига
     */
    public static function get(): static
    {
        return Container::isSet(static::getServiceName()) ? Container::get(static::getServiceName()) : new static();
    }

    /**
     * Отправляет письмо
     * @param string $subject Тема
     * @param string $to Адрес получателя
     * @param callable(Mail): void $fn Настройка письма
     * @return bool true, если отправлено успешно
     */
    public function send(string $subject, string $to, callable $fn): bool
    {
        $mail = new Mail();
        $mail->subject = $subject;
        $mail->to = $to;
        $fn($mail);

        return $this->transport->send($mail);
    }

    /**
     * Отправляет письмо с содержимым из шаблона
     * @param string $template Путь к шаблону, данные в нём - $data
     * @param bool $strip Альтернативное тело - текст без тегов
     */
    public function sendTemplate(string $subject, string $to, string $template, array $data = [], bool $strip = true): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($template, $data, $strip) {
            $mail->setBodyFromTemplate($template, $data, $strip);
        });
    }

    /**
     * Отправляет HTML-письмо
     * @param string $body Тело письма (HTML)
     * @param string|null $altBody Альтернативное тело, null - $body
     */
    public function sendText(string $subject, string $to, string $body, ?string $altBody = null): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($body, $altBody) {
            $mail->body = $body;
            $mail->altBody = $altBody ?? $body;
        });
    }

    /**
     * Отправляет письмо простым текстом: в HTML-тело текст попадает экранированным, с переносами строк
     */
    public function sendPlain(string $subject, string $to, string $text): bool
    {
        return $this->sendText($subject, $to, nl2br(htmlspecialchars($text)), $text);
    }
}

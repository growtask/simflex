<?php

namespace Simflex\Core\Mail;

use Simflex\Core\DI\Service;

/**
 * Mailer service
 */
class Mailer implements Service
{
    /**
     * @var Transport Mail transport
     */
    public Transport $transport;

    public static function getServiceName(): string
    {
        return 'mailer';
    }

    /**
     * Send mail
     * @param string $subject Subject
     * @param string $to Recipient address
     * @param callable $fn Mail object configure callback. Provides a single object of type Mail
     * @return bool "true" if sent successfully
     */
    public function send(string $subject, string $to, callable $fn): bool
    {
        // make sure transport is set
        if (!$this->transport) {
            throw new \AssertionError('Transport is not set');
        }

        // configure mail object
        $mail = new Mail();
        $mail->subject = $subject;
        $mail->to = $to;
        $fn($mail);

        // send it
        return $this->transport->send($mail);
    }

    /**
     * Send mail with templated content
     * @param string $subject Subject
     * @param string $to Recipient address
     * @param string $template Template to run
     * @param array $data Arbitrary template data
     * @param bool $strip Whether to strip tags from alt body
     * @return bool "true" if sent successfully
     */
    public function sendTemplate(string $subject, string $to, string $template, array $data = [], bool $strip = true): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($template, $data, $strip) {
            $mail->setBodyFromTemplate($template, $data, $strip);
        });
    }

    /**
     * Send mail with text content
     * @param string $subject Subject
     * @param string $to Recipient address
     * @param string $body Body
     * @param string|null $altBody Alt body (if set to null, will use $body instead)
     * @return bool "true" if sent successfully
     */
    public function sendText(string $subject, string $to, string $body, ?string $altBody = null): bool
    {
        return $this->send($subject, $to, function (Mail $mail) use ($body, $altBody) {
            $mail->body = $body;
            $mail->altBody = $altBody ?? $body;
        });
    }
}
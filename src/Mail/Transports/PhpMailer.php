<?php

namespace Simflex\Core\Mail\Transports;

use PHPMailer\PHPMailer\SMTP;
use Simflex\Core\System\Container;
use Simflex\Core\Logger\Log;
use Simflex\Core\Mail\Mail;
use Simflex\Core\Mail\Transport;
use Simflex\Core\System\Settings;

/**
 * Отправка по SMTP через PHPMailer. Сервер и учётная запись - конфиг mail (host, port, security, username,
 * password); отправитель - username (или noreply@<домен сайта>) с именем из настройки сайта site_name.
 * Ошибка соединения или отправки - исключение PHPMailer\PHPMailer\Exception
 */
class PhpMailer implements Transport
{
    public function send(Mail $mail): bool
    {
        $cfg = Container::getConfig();

        // настраиваем phpmailer
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = (string)$cfg->mail['host'];
        $mailer->Port = (int)$cfg->mail['port'] ?: 25;
        $mailer->SMTPSecure = (string)$cfg->mail['security'];
        $mailer->SMTPAuth = $cfg->mail['username'] !== '';
        $mailer->Username = (string)$cfg->mail['username'];
        $mailer->Password = (string)$cfg->mail['password'];
        $mailer->Timeout = 10;
        $mailer->FromName = (string)Settings::get('site_name');
        $mailer->From = str_contains((string)$cfg->mail['username'], '@')
            ? $cfg->mail['username']
            : 'noreply@' . (Container::getRequest()->getHost() ?: 'localhost');
        $mailer->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
        $mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false, // нужно для отправки через локальную сеть (например, подключение к 10.x.x.x)
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        if ($cfg->devMode || $cfg->enableLogging) {
            $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $mailer->Debugoutput = function ($str, $level) {
                Log::info('PHPMailer/{level}: {str}', ['level' => $level, 'str' => $str]);
            };
        }

        // заполняем адреса
        $mailer->addAddress($mail->to);
        array_walk($mail->cc, fn(string $str) => $mailer->addCC($str));
        array_walk($mail->bcc, fn(string $str) => $mailer->addBCC($str));
        if ($mail->replyTo) {
            $mailer->addReplyTo($mail->replyTo);
        }

        // заполняем вложения
        array_walk($mail->attachments, fn(string $str) => $mailer->addAttachment($str));

        // заполняем тело
        $mailer->isHTML();
        $mailer->Body = $mail->body;
        $mailer->AltBody = $mail->altBody;
        $mailer->Subject = $mail->subject;

        // отправляем
        return $mailer->send();
    }
}

<?php

namespace Simflex\Core\Mail\Transport;

use PHPMailer\PHPMailer\SMTP;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Log;
use Simflex\Core\Mail\Mail;
use Simflex\Core\Mail\Transport;

class PhpMailer implements Transport
{
    public function send(Mail $mail): bool
    {
        $cfg = Container::getConfig();

        // настраиваем phpmailer
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = $cfg->mail['security'];
        $mailer->Host = $cfg->mail['host'];
        $mailer->Username = $cfg->mail['username'];
        $mailer->Password = $cfg->mail['password'];
        $mailer->FromName = Core::siteParam('site_name');
        $mailer->From = $cfg->mail['username'];
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

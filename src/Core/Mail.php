<?php

namespace Simflex\Core;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Simflex\Core\Core;
use Simflex\Core\DB;

/**
 * @todo Incomplete: there are no migrations for the mail_log table, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class Mail
{
    public const MAIL_TRANSPORT_DAEMON = 1;
    public const MAIL_TRANSPORT_SMTP = 2;
    public const MAIL_TRANSPORT_FAKE_SUCCESS = 3;
    public const MAIL_TRANSPORT_FAKE_ERROR = 4;

    private array $data = [
        'to_email' => '', 'to_name' => '', 'from_name' => '', 'from_email' => '', 'as_plain' => false,
        'reply_email' => '', 'reply_name' => '', 'text' => '', 'subject' => '', 'can_answer' => false,
        'transport' => self::MAIL_TRANSPORT_DAEMON,
    ];

    /**
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $msg
     */
    public function __construct($toEmail, $subject, $msg)
    {
        $this->data['from_name'] = Core::siteParam('site_name');
        $this->data['from_email'] = "noreply@{$_SERVER['HTTP_HOST']}";

        $this->data['to_email'] = $toEmail;
        $this->data['subject'] = $subject;
        $this->data['text'] = $msg;

        if (!empty(Core::siteParam('send_deny_email'))) {
            $this->data['transport'] = self::MAIL_TRANSPORT_FAKE_SUCCESS;
        }
    }

    /**
     * Создает объект Mail
     * @param string $toEmail
     * @param string $subject
     * @param string $msg
     */
    public static function create($toEmail, $subject, $msg): static
    {
        $instance = new static($toEmail, $subject, $msg);
        return $instance;
    }

    public function setToName($name): static
    {
        $this->data['to_name'] = trim($name);
        return $this;
    }

    public function setTransport($transport): static
    {
        $this->data['transport'] = $transport;
        return $this;
    }

    /**
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $msg
     * @return bool
     */
    public static function staticSend($toEmail, $subject, $msg): mixed
    {
        $instance = new static($toEmail, $subject, $msg);
        return $instance->send();
    }

    /**
     *
     * @param string $str Допускается: Иванов Иван или ivanov@mail.ru или Иванов Иван <ivanov@mail.ru>
     * @param string $str2 (optional = '') Имя отправителя. Имеет значение, если $str - это email
     */
    public function setFrom($str, $str2 = ''): static
    {
        if (strpos($str, '<') !== false) {
            $matches = [];
            preg_match('@(.*)\s?<(.*)>@Ui', $str, $matches);
            if (count($matches)) {
                $this->data['from_name'] = trim($matches[1]);
                $this->data['from_email'] = trim($matches[2]);
            }
        } elseif (strpos($str, '@') !== false) {
            $this->data['from_email'] = trim($str);
            if ($str2) {
                $this->data['from_name'] = trim($str2);
            }
        } else {
            $this->data['from_name'] = trim($str);
        }
        return $this;
    }

    /**
     * Допускается: Иванов Иван или ivanov@mail.ru или Иванов Иван <ivanov@mail.ru>
     * @param string $str
     */
    public function setReplyTo($str): static
    {
        if (strpos($str, '<') !== false) {
            $matches = [];
            preg_match('@(.*)\s?<(.*)>@Ui', $str, $matches);
            if (count($matches)) {
                $this->data['reply_name'] = trim($matches[1]);
                $this->data['reply_email'] = trim($matches[2]);
            }
        } elseif (strpos($str, '@') !== false) {
            $this->data['reply_email'] = trim($str);
        } else {
            $this->data['reply_name'] = trim($str);
        }
        return $this;
    }

    /**
     * По умолчанию отправлять как HTML
     */
    public function setAsPlain(): static
    {
        $this->data['as_plain'] = true;
        return $this;
    }

    /**
     * По умолчанию уже отправлять как HTML
     */
    public function setAsHTML(): static
    {
        $this->data['as_plain'] = false;
        return $this;
    }

    /**
     *
     * @param string $sender Допускается mail@mail.ru или mail@mail.ru:12345
     * @param string $pass (optional = false) указывается, если в $sender нет пароля
     */
    public function setSMTPSender($sender, $pass = false): static
    {
        if (!$pass) {
            $tmp = explode(':', (string)$sender, 2);
            $sender = $tmp[0];
            $pass = $tmp[1] ?? '';
        }
        $this->data['from_email'] = $sender;
        $this->data['smtp'] = ['login' => $sender, 'pass' => $pass];
        return $this;
    }

    /**
     * Используется чтобы убрать внизу текст "Отвечать на данное письмо ненужно"
     * @param bool $can
     */
    public function setCanAnswer($can): static
    {
        $this->data['can_answer'] = $can;
        return $this;
    }

    /**
     * @return bool
     */
    public function send(): mixed
    {
        $result = false;

        if (!$this->data['as_plain'] && is_file(SF_ROOT_PATH . '/theme/default/mail.tpl')) {
            ob_start();
            $toEmail = $this->data['to_email'];
            $httpHost = "http://{$_SERVER['HTTP_HOST']}";

            $mailContent = $this->data['text'];
            $canAnswer = $this->data['can_answer'];
            include SF_ROOT_PATH . '/theme/default/mail.tpl';
            $this->data['text'] = ob_get_clean();

            $siteParams = Core::siteParam();
            foreach ($siteParams as $paramAlias => $paramValue) {
                $this->data['text'] = str_replace('{' . $paramAlias . '}', $paramValue, $this->data['text']);
            }
            $this->data['text'] = str_replace('{year}', date('Y'), $this->data['text']);
        }
        $this->data['text_utf8'] = $this->data['text'];
        $this->data['subject_utf8'] = $this->data['subject'];

        if (md5(@iconv('utf-8', 'utf-8', $this->data['subject'])) == md5($this->data['subject'])) {
            $this->data['subject'] = @iconv('utf-8', 'windows-1251', $this->data['subject']);
        }
        if (md5(@iconv('utf-8', 'utf-8', $this->data['text'])) == md5($this->data['text'])) {
            $this->data['text'] = @iconv('utf-8', 'windows-1251', $this->data['text']);
        }

        $toName = $this->data['to_name'];
        if ($toName && md5(@iconv('utf-8', 'utf-8', $toName)) == md5($toName)) {
            $toName = @iconv('utf-8', 'windows-1251', $toName);
        }
        $toEnc = '=?windows-1251?B?' . base64_encode($toName) . '?=';
        $this->data['to_full'] = "$toEnc <{$this->data['to_email']}>";

        $fromName = $this->data['from_name'];
        if ($fromName && md5(@iconv('utf-8', 'utf-8', $fromName)) == md5($fromName)) {
            $fromName = @iconv('utf-8', 'windows-1251', $fromName);
        }
        $fromEnc = '=?windows-1251?B?' . base64_encode($fromName) . '?=';
        $this->data['from_full'] = "$fromEnc <{$this->data['from_email']}>";

        if ($this->data['reply_email']) {
            $replyName = $this->data['reply_name'];
            if ($replyName && md5(@iconv('utf-8', 'utf-8', $replyName)) == md5($replyName)) {
                $replyName = @iconv('utf-8', 'windows-1251', $replyName);
            }
            $replyEnc = '=?windows-1251?B?' . base64_encode($replyName) . '?=';
            $this->data['reply_full'] = "$replyEnc <{$this->data['reply_email']}>";
        }

        $this->data['subject_ready'] = '=?windows-1251?B?' . base64_encode($this->data['subject']) . '?=';

        switch ($this->data['transport']) {
            case self::MAIL_TRANSPORT_DAEMON: $result = $this->sendDaemon();
                break;
            case self::MAIL_TRANSPORT_SMTP: $result = $this->sendSMTP();
                break;
            case self::MAIL_TRANSPORT_FAKE_SUCCESS: $result = true;
                break;
            case self::MAIL_TRANSPORT_FAKE_ERROR: default: $result = false;
        }

        $this->toLog($result);

        return $result;
    }

    private function toLog($result): void
    {
        $q = 'DELETE FROM mail_log WHERE ADDDATE(time_create,INTERVAL 1 MONTH) < NOW()';
        DB::query($q);
        $set = ["mail_from = '" . DB::escape($this->data['from_full']) . "', email = '" . DB::escape($this->data['to_full']) . "'"];
        $set[] = "text = '" . DB::escape($this->data['text_utf8']) . "', subject = '" . DB::escape($this->data['subject_ready']) . "'";
        $set[] = 'success = ' . (int) $result . ", transport = {$this->data['transport']}";
        if (!empty($this->data['reply_full'])) {
            $set[] = "reply_to = '" . DB::escape($this->data['reply_full']) . "'";
        }
        $q = 'INSERT INTO mail_log SET ' . implode(', ', $set);
        $success = DB::query($q);
    }

    /**
     * Отправка через стандартную функцию php mail
     */
    private function sendDaemon(): bool
    {
        $contentType = $this->data['as_plain'] ? 'plain' : 'html';
        $header = "Content-type: text/$contentType; charset=\"windows-1251\"\n";
        $header .= 'MIME-Version: 1.0' . "\n";
        $header .= "From: {$this->data['from_full']}\n";
        if ($this->data['reply_email']) {
            $header .= "Reply-To: {$this->data['reply_full']}\n";
        }
        $header .= "Subject: {$this->data['subject_ready']}";
        return mail($this->data['to_full'], $this->data['subject_ready'], $this->data['text'], $header);
    }

    /**
     * Отправка через SMTP (PHPMailer). Сервер берется из Config::$mail (host, port, security).
     * Логин/пароль: setSMTPSender() > Config::$mail (username, password) > параметр сайта "sender" (login:pass).
     */
    private function sendSMTP(): bool
    {
        $cfgMail = Container::getConfig()->mail;

        if (empty($this->data['smtp']['login']) && !empty($cfgMail['username']) && !empty($cfgMail['password'])) {
            $this->setSMTPSender($cfgMail['username'], $cfgMail['password']);
        }
        if (empty($this->data['smtp']['login']) && Core::siteParam('sender')) {
            $this->setSMTPSender(Core::siteParam('sender'));
        }
        if (empty($this->data['smtp']['login']) || empty($this->data['smtp']['pass'])) {
            Log::error('Mail SMTP: credentials are not set');
            return false;
        }

        $mailer = new PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host = (string)($cfgMail['host'] ?? 'localhost');
            $mailer->Port = (int)($cfgMail['port'] ?? 25) ?: 25;
            $mailer->SMTPSecure = (string)($cfgMail['security'] ?? '');
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->data['smtp']['login'];
            $mailer->Password = $this->data['smtp']['pass'];
            $mailer->Timeout = 10;
            $mailer->CharSet = PHPMailer::CHARSET_UTF8;

            $mailer->setFrom($this->data['from_email'], (string)$this->data['from_name'], false);
            $mailer->addAddress($this->data['to_email'], (string)$this->data['to_name']);
            if ($this->data['reply_email']) {
                $mailer->addReplyTo($this->data['reply_email'], (string)$this->data['reply_name']);
            }
            $mailer->isHTML(!$this->data['as_plain']);
            $mailer->Subject = $this->data['subject_utf8'] ?? $this->data['subject'];
            $mailer->Body = $this->data['text_utf8'] ?? $this->data['text'];

            return $mailer->send();
        } catch (PHPMailerException $e) {
            Log::error('Mail SMTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Рекурсивно конвертирует массив или строку. У массива ключи тоже конвертируются
     * @param string|array $mixed
     */
    private static function utf2win($mixed): array|string|false
    {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                unset($mixed[$index]);
                $mixed[self::utf2win($index)] = self::utf2win($str);
            }
            return $mixed;
        }
        return iconv('utf-8', 'windows-1251', $mixed);
    }
}

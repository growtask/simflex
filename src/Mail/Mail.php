<?php

namespace Simflex\Core\Mail;

use Simflex\Core\Http\View;

/**
 * Письмо.
 */
class Mail
{
    /**
     * @var string Тема
     */
    public string $subject = '';

    /**
     * @var string Тело (может содержать HTML)
     */
    public string $body = '';

    /**
     * @var string Альтернативное тело (только текст)
     */
    public string $altBody = '';

    /**
     * @var string Адрес получателя
     */
    public string $to = '';

    /**
     * @var string Адрес Reply-To
     */
    public string $replyTo = '';

    /**
     * @var array Дополнительные получатели
     */
    public array $cc = [];

    /**
     * @var array Дополнительные скрытые получатели
     */
    public array $bcc = [];

    /**
     * @var array Вложения
     */
    public array $attachments = [];

    /**
     * Рендерит шаблон и задает тело и альтернативное тело.
     * @param string $templatePath Путь к файлу шаблона
     * @param array $data Дополнительные произвольные данные
     * @param bool $strip Вырезать ли теги из тела
     */
    public function setBodyFromTemplate(string $templatePath, array $data = [], bool $strip = true): void
    {
        $this->body = View::render($templatePath, ['data' => $data], $this);

        if ($strip) {
            // абзацы и переносы - переводы строк, остальные теги убираются
            $this->altBody = trim(strip_tags(preg_replace(['#</p>#i', '#<br\s*/?>#i'], "\n", $this->body)));
        } else {
            $this->altBody = $this->body;
        }
    }
}

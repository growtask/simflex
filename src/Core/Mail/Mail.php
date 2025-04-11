<?php

namespace Simflex\Core\Mail;

/**
 * Mail object.
 */
class Mail
{
    /**
     * @var string Subject
     */
    public string $subject = '';

    /**
     * @var string Body (may contain HTML)
     */
    public string $body = '';

    /**
     * @var string Alternative body (text-only)
     */
    public string $altBody = '';

    /**
     * @var string Recipient address
     */
    public string $to = '';

    /**
     * @var string Reply-To address
     */
    public string $replyTo = '';

    /**
     * @var array Additional recipiets
     */
    public array $cc = [];

    /**
     * @var array Additional secret recipients
     */
    public array $bcc = [];

    /**
     * @var array Attachments
     */
    public array $attachments = [];

    /**
     * Runs a template and sets body and alt body.
     * @param string $templatePath Path to the template file
     * @param array $data Additional arbitrary data
     * @param bool $strip Whether should strip tags from body
     * @return void
     */
    public function setBodyFromTemplate(string $templatePath, array $data = [], bool $strip = true): void
    {
        ob_start();
        include $templatePath;
        $this->body = ob_get_clean();

        if ($strip) {
            // replace </p> and <br/> with \n
            $body = $this->body;
            $body = preg_replace(['<\\/p>', '<br\\/?>'], "\n", $body);

            // strip all other tags
            $body = preg_replace('<[a-zA-Z\\-\\s\\/?]+>', '', $body);
            $this->altBody = $body;
        } else {
            $this->altBody = $this->body;
        }
    }
}
<?php

namespace Simflex\Core\Html;

use Simflex\Core\Response;
use Simflex\Core\View;

class HtmlResponse extends Response
{
    protected string $content = '';

    public function __construct()
    {
        parent::__construct();
        $this->setContentType('text/html');
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    public function addContentFromTemplate(string $templatePath, array $params): void
    {
        if (file_exists($templatePath)) {
            $this->addContent(View::render($templatePath, $params, $this));
        }
    }

    protected function makeBody(): string
    {
        return $this->content;
    }
}

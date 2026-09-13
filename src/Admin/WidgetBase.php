<?php

namespace Simflex\Admin;

use Simflex\Core\View;

abstract class WidgetBase
{
    protected array $data;
    protected int $id;

    public function __construct($data)
    {
        $this->data = $data;
        $this->id = (int)$this->data['widget_id'];
    }

    /**
     * Widget content. Returns HTML, does not output it
     */
    abstract protected function content(): string;

    /**
     * Widget header actions HTML
     */
    protected function actions(): string
    {
        return '';
    }

    final public function execute(): string
    {
        $content = $this->content();
        if (Core::ajax() || $content === '') {
            return $content;
        }

        return View::render(__DIR__ . '/theme/tpl/widget.layout.tpl', ['content' => $content], $this);
    }
}

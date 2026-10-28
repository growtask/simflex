<?php

namespace Simflex\Core\Admin;

use Simflex\Core\Http\View;

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
     * Содержимое виджета. Возвращает HTML, а не выводит его
     */
    abstract protected function content(): string;

    /**
     * HTML действий в заголовке виджета
     */
    protected function actions(): string
    {
        return '';
    }

    final public function execute(): string
    {
        $content = $this->content();
        if (Page::get()->isAjax() || $content === '') {
            return $content;
        }

        return View::render(__DIR__ . '/theme/tpl/widget.layout.tpl', ['content' => $content], $this);
    }
}

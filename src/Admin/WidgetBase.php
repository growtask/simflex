<?php

namespace Simflex\Admin;

abstract class WidgetBase
{
    protected array $data;
    protected int $id;

    public function __construct($data)
    {
        $this->data = $data;
        $this->id = (int)$this->data['widget_id'];
    }

    abstract protected function content(): void;

    protected function actions(): void
    {

    }

    final public function execute(): string|false
    {
        if (Core::ajax()) {
            ob_start();
            $this->content();
            return ob_get_clean();
        } else {
            ob_start();
            $this->content();
            $content = ob_get_clean();
            if ($content) {
                ob_start();
                include __DIR__ . '/theme/tpl/widget.layout.tpl';
                return ob_get_clean();
            }
        }
        return '';
    }
}

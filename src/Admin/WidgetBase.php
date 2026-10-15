<?php

namespace Simflex\Admin;

use Simflex\Admin\Core;

abstract class WidgetBase
{

    protected array $data;
    protected int $id;

    public function __construct($data)
    {
        $this->data = $data;
        $this->id = (int)$this->data['widget_id'];
    }

    protected abstract function content(): void;

    protected function actions(): void
    {

    }

    public final function execute(): string|false
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

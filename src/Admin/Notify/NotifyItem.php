<?php

namespace Simflex\Admin\Notify;


class NotifyItem
{

    private string $text = '';
    private string $icon = 'bolt';
    private string $href = 'javascript:;';
    private array $data = [];

    public function __construct($text, $href = '', $icon = '')
    {
        $this->text = $text;
        $icon ? $this->icon = $icon : null;
        $href ? $this->href = $href : null;
    }

    public function html(): void
    {
        include 'tpl/item.tpl';
    }

}

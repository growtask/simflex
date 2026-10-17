<?php

namespace Simflex\Admin\Notify;

use Simflex\Core\View;

/**
 * @incomplete The Notify subsystem is not wired into the admin panel yet; it will be finished and used later.
 */
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

    public function html(): string
    {
        return View::render(__DIR__ . '/tpl/item.tpl', [], $this);
    }
}

<?php

namespace Simflex\Admin\Notify;

use Simflex\Core\View;

/**
 * @deprecated The Notify subsystem is not used by the admin panel and will be removed.
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

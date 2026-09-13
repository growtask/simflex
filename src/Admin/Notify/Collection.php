<?php

namespace Simflex\Admin\Notify;

use Simflex\Core\View;

/**
 * @deprecated The Notify subsystem is not used by the admin panel and will be removed.
 */
class Collection
{
    private static self $instance;

    public static function getInstance(): static
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private array $items = [];

    private function __construct()
    {

    }

    public function content(): string
    {
        $this->collect();
        return View::render(__DIR__ . '/tpl/index.tpl', ['cnt' => count($this->items)], $this);
    }

    private function collect(): void
    {
        $extRoot = SF_ROOT_PATH . '/ext';
        $exts = array_slice(scandir($extRoot), 2);
        foreach ($exts as $ext) {
            $class = 'AdminNotify' . ucfirst($ext);
            if (class_exists($class)) {
                $items = $class::get();
                if (is_array($items) && count($items)) {
                    $this->items = array_merge($this->items, $items);
                }
            }
        }
    }
}

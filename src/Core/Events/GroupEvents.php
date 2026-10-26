<?php

namespace Simflex\Core\Events;

use Simflex\Core\DI\Injector;

/**
 * Реализует onEvent так, что вызывается метод по имени события
 *
 * Например, для события Events::PreInit будет вызван метод onPreInit
 * Аргументы метода: Event $event, ...$params
 */
trait GroupEvents
{
    public function onEvent(Event $event): void
    {
        $eventName = 'on' . $event->getName();
        if (method_exists($this, $eventName)) {
            $this->$eventName(...Injector::resolveMethod($this, $eventName, $event, ...$event->getParams()));
        }
    }
}

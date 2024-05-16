<?php

namespace Simflex\Core\Events;

use Simflex\Core\DI\Injector;

trait GroupEvents
{
    public function onEvent(Event $event)
    {
        $eventName = 'on' . ucfirst($event->getName());
        if (method_exists($this, $eventName)) {
            $this->$eventName(...Injector::resolveMethod($this, $eventName, $event, ...$event->getParams()));
        }
    }
}
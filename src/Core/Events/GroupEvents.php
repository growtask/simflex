<?php

namespace Simflex\Core\Events;

use Simflex\Core\DI\Injector;
use Simflex\Core\Log;

trait GroupEvents
{
    public function onEvent(Event $event)
    {
        $eventName = 'on' . str_replace('_', '', $event->getName());
        if (method_exists($this, $eventName)) {
            $this->$eventName(...Injector::resolveMethod($this, $eventName, $event, ...$event->getParams()));
        }
    }
}
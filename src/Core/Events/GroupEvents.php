<?php

namespace Simflex\Core\Events;

use Simflex\Core\DependencyInjection;

trait GroupEvents
{
    public function onEvent(Event $event)
    {
        $eventName = $event->getName();
        if (method_exists($this, $eventName)) {
            $this->$eventName($event, ...DependencyInjection::resolveMethod($this, $eventName, $event->getParams()));
        }
    }
}
<?php

namespace Simflex\Core\Events;

use Simflex\Core\DI\Injector;
use Simflex\Core\Log;

/**
 * Implements onEvent in a way that allows to call methods with event name
 *
 * For example, if event name is Events::PreInit, then method onPreInit will be called
 * Method arguments are: Event $event, ...$params
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
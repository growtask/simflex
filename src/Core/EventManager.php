<?php

namespace Simflex\Core;

class EventManager
{
    protected array $listeners = [];

    /**
     * Subscribes a listener to an event
     *
     * @param string $eventName Event name
     * @param string $listenerClass Listener class (must implement EventListener interface)
     * @return void
     */
    public function subscribe(string $eventName, string $listenerClass)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $this->listeners[$eventName][] = new $listenerClass();
    }

    /**
     * Subscribes many listeners to an event
     *
     * @param string $eventName Event name
     * @param array $listenerClasses Array of listener classes (must implement EventListener interface)
     * @return void
     */
    public function subscribeMany(string $eventName, array $listenerClasses)
    {
        foreach ($listenerClasses as $listenerClass) {
            $this->subscribe($eventName, $listenerClass);
        }
    }

    /**
     * Subscribes many listeners to many events
     *
     * @param array $subscribers Array of event names and listener classes
     * @return void
     */
    public function subscribeAll(array $subscribers)
    {
        foreach ($subscribers as $eventName => $listenerClasses) {
            $this->subscribeMany($eventName, $listenerClasses);
        }
    }

    /**
     * Fires an event
     *
     * @param Event $event Event object
     * @return void
     */
    public function dispatch(Event $event)
    {
        $eventName = $event->getName();
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener->onEvent($event);
            }
        }
    }
}
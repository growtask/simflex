<?php

namespace Simflex\Core;

use Simflex\Core\Events\Event;

class EventManager implements \Simflex\Core\DI\Service
{
    protected array $listeners = [];

    public static function getServiceName(): string
    {
        return 'events';
    }

    /**
     * Подписывает слушателя на событие
     *
     * @param string $eventName Имя события
     * @param string $listenerClass Класс слушателя (должен реализовывать интерфейс EventListener)
     */
    public function subscribe(string $eventName, string $listenerClass): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $this->listeners[$eventName][] = new $listenerClass();
    }

    /**
     * Подписывает несколько слушателей на событие
     *
     * @param string $eventName Имя события
     * @param array $listenerClasses Массив классов слушателей (должны реализовывать интерфейс EventListener)
     */
    public function subscribeMany(string $eventName, array $listenerClasses): void
    {
        foreach ($listenerClasses as $listenerClass) {
            $this->subscribe($eventName, $listenerClass);
        }
    }

    /**
     * Подписывает несколько слушателей на несколько событий
     *
     * @param array $subscribers Массив имен событий и классов слушателей
     */
    public function subscribeAll(array $subscribers): void
    {
        foreach ($subscribers as $eventName => $listenerClasses) {
            $this->subscribeMany($eventName, $listenerClasses);
        }
    }

    /**
     * Вызывает событие
     *
     * @param Event $event Объект события
     */
    public function dispatch(Event $event): void
    {
        $subject = $event->getSubject();
        if (isset($this->listeners[$subject])) {
            foreach ($this->listeners[$subject] as $listener) {
                $listener->onEvent($event);
            }
        }
    }
}

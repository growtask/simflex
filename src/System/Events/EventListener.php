<?php

namespace Simflex\Core\System\Events;

interface EventListener
{
    public function onEvent(Event $event): void;
}

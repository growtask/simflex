<?php

namespace Simflex\Core\Events;

interface EventListener
{
    public function onEvent(Event $event);
}
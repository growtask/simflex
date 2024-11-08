<?php

namespace Simflex\Core\Events;

class Event
{
    protected array $params;

    public function __construct(
        protected Events $ev,
        protected string $subject,
        ...$params
    ) {
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->ev->name;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
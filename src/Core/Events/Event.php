<?php

namespace Simflex\Core\Events;

class Event
{
    protected array $params;

    public function __construct(
        protected string $name,
        protected string $subject,
        ...$params
    ) {
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
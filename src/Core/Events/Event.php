<?php

namespace Simflex\Core\Events;

class Event
{
    protected array $params;

    public function __construct(protected string $name, protected $object = null, array ...$params)
    {
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
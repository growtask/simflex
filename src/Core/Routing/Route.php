<?php

namespace Simflex\Core\Routing;

class Route
{
    public function __construct(protected string $componentClassName, protected string $baseUri, protected ?string $componentAction = null)
    {
    }

    public function getComponentClassName(): string
    {
        return $this->componentClassName;
    }

    public function getComponentAction(): ?string
    {
        return $this->componentAction;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }
}
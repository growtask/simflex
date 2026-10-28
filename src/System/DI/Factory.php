<?php

namespace Simflex\Core\System\DI;

class Factory implements \Simflex\Core\System\DI\Service
{
    protected array $overrides = [];

    public static function getServiceName(): string
    {
        return 'factory';
    }

    public function override(string $class, string $override): void
    {
        $this->overrides[$class] = $override;
    }

    public function getStatic(string $class): string
    {
        if (isset($this->overrides[$class])) {
            $class = $this->overrides[$class];
        }

        return $class;
    }

    public function create(string $class, ...$params): object
    {
        if (isset($this->overrides[$class])) {
            $class = $this->overrides[$class];
        }

        return new $class(...$params);
    }
}

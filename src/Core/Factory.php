<?php
namespace Simflex\Core;

class Factory
{
    protected array $overrides = [];

    public function override(string $class, string $override): void
    {
        $this->overrides[$class] = $override;
    }

    public function getStatic(string $class)
    {
        if (isset($this->overrides[$class])) {
            $class = $this->overrides[$class];
        }

        return $class;
    }

    public function create(string $class, array $params = [])
    {
        if (isset($this->overrides[$class])) {
            $class = $this->overrides[$class];
        }

        return new $class(...$params);
    }
}
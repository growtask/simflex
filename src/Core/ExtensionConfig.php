<?php
namespace Simflex\Core;

abstract class ExtensionConfig
{
    public string $name;

    protected array $routes = [];
    protected array $services = [];
    protected array $events = [];
    protected array $commands = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
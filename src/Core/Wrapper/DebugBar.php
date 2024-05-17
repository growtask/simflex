<?php

namespace Simflex\Core\Wrapper;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\StandardDebugBar;
use Simflex\Core\Container;
use Simflex\Core\DI\Service;

class DebugBar implements Service
{
    protected \DebugBar\DebugBar $debugBar;

    public static function getServiceName(): string
    {
        return 'debugbar';
    }

    public function __construct()
    {
        if (!Container::getConfig()::$devMode) {
            return;
        }

        $this->debugBar = new \DebugBar\DebugBar();
        $this->debugBar->addCollector(new MessagesCollector());
        $this->debugBar->addCollector(new TimeDataCollector());
        $this->debugBar->addCollector(new RequestDataCollector());
        $this->debugBar->addCollector(new MemoryCollector());
    }

    public function addCollector(DataCollectorInterface $collector)
    {
        $this->debugBar->addCollector($collector);
    }

    public function renderHead()
    {
        echo $this->debugBar->getJavascriptRenderer()->renderHead();
    }

    public function render()
    {
        echo $this->debugBar->getJavascriptRenderer()->render();
    }

    public function getMessages(): MessagesCollector
    {
        return $this->debugBar['messages'];
    }

    public function getTime(): TimeDataCollector
    {
        return $this->debugBar['time'];
    }

    public function getRequest(): RequestDataCollector
    {
        return $this->debugBar['request'];
    }

    public function getMemory(): MemoryCollector
    {
        return $this->debugBar['memory'];
    }
}
<?php

namespace Simflex\Core\Wrapper;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\JavascriptRenderer;
use Simflex\Core\Assets;
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
        // deprecated
        if (!Container::getConfig()->devMode) {
            return;
        }

        $this->debugBar = new \DebugBar\DebugBar();
        $this->debugBar->addCollector(new MessagesCollector());
        $this->debugBar->addCollector(new TimeDataCollector());
        $this->debugBar->addCollector(new RequestDataCollector());
        $this->debugBar->addCollector(new MemoryCollector());
    }

    public function addCollector(DataCollectorInterface $collector): void
    {
        $this->debugBar->addCollector($collector);
    }

    public function renderHead(): string
    {
        return $this->renderer()->renderHead();
    }

    public function render(): string
    {
        return $this->renderer()->render();
    }

    /**
     * Ресурсы DebugBar лежат в vendor и публикуются в SF_PUBLIC_PATH
     */
    protected function renderer(): JavascriptRenderer
    {
        $renderer = $this->debugBar->getJavascriptRenderer();
        $renderer->setBaseUrl(Assets::publish($renderer->getBasePath()));
        return $renderer;
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

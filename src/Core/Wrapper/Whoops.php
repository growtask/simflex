<?php

namespace Simflex\Core\Wrapper;

use Simflex\Core\Container;
use Simflex\Core\DI\Service;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops implements Service
{
    protected Run $whoops;

    public static function getServiceName(): string
    {
        return 'whoops';
    }

    public function __construct()
    {
        if (!Container::getConfig()::$devMode) {
            return;
        }

        $this->whoops = new Run();
        $this->whoops->pushHandler(new PrettyPageHandler());
        $this->whoops->register();
    }
}
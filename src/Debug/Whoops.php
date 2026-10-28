<?php

namespace Simflex\Debug;

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
        $this->whoops = new Run();
        $this->whoops->pushHandler(new PrettyPageHandler());
        $this->whoops->register();
    }
}

<?php

namespace Simflex\Core\Debug;

use Simflex\Core\System\DI\Service;
use Whoops\Handler\PlainTextHandler;
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
        // PrettyPageHandler в консоли ничего не выводит: исключение команды ./sf пропадало бы молча
        $this->whoops->pushHandler(PHP_SAPI === 'cli' ? new PlainTextHandler() : new PrettyPageHandler());
        $this->whoops->register();
    }
}

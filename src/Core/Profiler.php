<?php

namespace Simflex\Core;

use DebugBar\DataCollector\TimeDataCollector;
use JetBrains\PhpStorm\Deprecated;

class Profiler
{
    #[Deprecated(reason: 'Starts automatically, no need to invoke this function.')]
    public static function start(): void
    {
    }

    public static function traceStart(object|string $obj, string $func, #[Deprecated] string $type = ''): void
    {
        if (!Container::getConfig()->devMode) {
            return;
        }

        if (!is_string($obj)) {
            $obj = get_class($obj);
        }

        /** @var TimeDataCollector $time */
        $time = Container::get('debugbar')->getTime();
        $time->startMeasure($obj . '::' . $func, $obj . '::' . $func);
    }

    public static function traceEnd(object|string $obj = '', string $func = '', #[Deprecated] string $type = ''): void
    {
        if (!Container::getConfig()->devMode) {
            return;
        }

        if (!$obj || !$func) {
            throw new \Exception('Profiler::traceEnd() requires 2 arguments');
        }

        if (!is_string($obj)) {
            $obj = get_class($obj);
        }

        /** @var TimeDataCollector $time */
        $time = Container::get('debugbar')->getTime();
        $time->stopMeasure($obj . '::' . $func);
    }

    #[Deprecated(reason: 'Automatic output')]
    private static function outputArr($arr, $i = 0): void
    {
    }

    #[Deprecated(reason: 'Automatic output')]
    public static function output(): void
    {
    }
}
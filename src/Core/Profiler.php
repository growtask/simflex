<?php

namespace Simflex\Core;

use DebugBar\DataCollector\TimeDataCollector;
use JetBrains\PhpStorm\Deprecated;

class Profiler
{
    #[Deprecated(reason: 'Starts automatically, no need to invoke this function.')]
    public static function start()
    {
    }

    public static function traceStart($obj, string $func, #[Deprecated] string $type = '')
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

    public static function traceEnd($obj = '', string $func = '', #[Deprecated] string $type = '')
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
    private static function outputArr($arr, $i = 0)
    {
    }

    #[Deprecated(reason: 'Automatic output')]
    public static function output()
    {
    }
}
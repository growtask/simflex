<?php

namespace Simflex\Core;

use DebugBar\DataCollector\TimeDataCollector;

class Profiler
{
    public static function traceStart(object|string $obj, string $func): void
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

    public static function traceEnd(object|string $obj, string $func): void
    {
        if (!Container::getConfig()->devMode) {
            return;
        }

        if (!is_string($obj)) {
            $obj = get_class($obj);
        }

        /** @var TimeDataCollector $time */
        $time = Container::get('debugbar')->getTime();
        $time->stopMeasure($obj . '::' . $func);
    }
}

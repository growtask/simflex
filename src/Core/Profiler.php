<?php
namespace Simflex\Core;

class Profiler
{
    protected static $startTime = 0;
    protected static $trace = [];
    protected static $traceOrd = [];
    public static $traceStack = [];

    public static function start()
    {
        if (!env('PROFILER')) {
            return;
        }

        self::$startTime = microtime(true);
    }

    public static function traceSingle($obj, string $func, string $type = 'echo')
    {
        self::traceStart($obj, $func, $type);
        self::traceEnd($obj, $func, $type);
    }

    public static function traceStart($obj, string $func, string $type = 'function')
    {
        if (!env('PROFILER')) {
            return;
        }

        $data = [
            'type' => $type,
            'func' => $func,
            'obj' => is_object($obj) ? get_class($obj) : $obj,
            'time' => microtime(true),
        ];

        $id = md5($func . '.' . $type . '.' . microtime());
        if (!count(self::$traceStack)) {
            self::$trace[$id]['start'] = $data;
            self::$trace[$id]['stack'] = [];
        } else {
            $stack = null;
            foreach (self::$traceStack as $tr) {
                if (!$stack) {
                    $stack = &self::$trace[$tr]['stack'];
                } else {
                    $stack = &$stack[$tr]['stack'];
                }
            }

            $stack[$id]['start'] = $data;
            $stack[$id]['stack'] = [];
        }

        array_push(self::$traceStack, $id);
    }

    public static function traceEnd($obj = '', string $func = '', string $type = 'function')
    {
        if (!env('PROFILER')) {
            return;
        }

        $data = [
            'time' => microtime(true),
        ];

        $id = array_pop(self::$traceStack);
        if (!count(self::$traceStack)) {
            self::$trace[$id]['end'] = $data;
        } else {
            $stack = null;
            foreach (self::$traceStack as $tr) {
                if (!$stack) {
                    $stack = &self::$trace[$tr]['stack'];
                } else {
                    $stack = &$stack[$tr]['stack'];
                }
            }

            $stack[$id]['end'] = $data;
        }
    }

    private static function outputArr($arr, $i = 0)
    {
        foreach ($arr as $id=>$v) {
            $tr = $v['start'];
            $trE = $v['end'];

            echo str_repeat("\t", $i);
            echo '-> ';

            if ($tr['type'] == 'function') {
                echo $tr['obj'] . '::' . $tr['func'];
            } else {
                echo 'QUERY: "' . $tr['func'] . '"';
            }

            echo "\t";
            echo 'exec time: ' . number_format($trE['time'] - $tr['time'], 3) . ' sec';
            echo "\n";

            if ($v['stack']) {
                self::outputArr($v['stack'], $i + 1);
            }
        }
    }

    public static function output()
    {
        if (!env('PROFILER')) {
            return;
        }

        echo '<pre>';

        $totalTime = microtime(true) - self::$startTime;

        echo 'Total execution time: ' . number_format($totalTime, 3) . ' sec' . "\n\n";
        self::outputArr(self::$trace);
        echo '</pre>';
    }
}
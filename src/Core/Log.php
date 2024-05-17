<?php

namespace Simflex\Core;

use Psr\Log\LoggerInterface;
use Simflex\Core\Container;

/**
 * PSR-3 logger facade
 *
 * @method static void emergency(string|\Stringable $message, array $context = [])
 * @method static void alert(string|\Stringable $message, array $context = [])
 * @method static void critical(string|\Stringable $message, array $context = [])
 * @method static void error(string|\Stringable $message, array $context = [])
 * @method static void warning(string|\Stringable $message, array $context = [])
 * @method static void notice(string|\Stringable $message, array $context = [])
 * @method static void info(string|\Stringable $message, array $context = [])
 * @method static void debug(string|\Stringable $message, array $context = [])
 * @method static void log(string|\Stringable $level, string|\Stringable $message, array $context = [])
 */
class Log
{
    /** @var LoggerInterface[] */
    protected static array $loggers;

    public static function addLogger(LoggerInterface $logger)
    {
        self::$loggers[] = $logger;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        foreach (self::$loggers as $logger) {
            if (method_exists($logger, $name)) {
                $logger->$name(...$arguments);
            }
        }
    }
}

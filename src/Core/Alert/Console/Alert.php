<?php

namespace Simflex\Core\Alert\Console;

use JetBrains\PhpStorm\Deprecated;
use Simflex\Core\Log;

#[Deprecated]
class Alert
{
    public static function text($message): void
    {
        Log::notice($message);
    }

    public static function error($message): void
    {
        Log::error($message);
    }

    public static function warning($message): void
    {
        Log::warning($message);
    }

    public static function success($message): void
    {
        Log::info($message);
    }

    public static function result($success, $successMessage, $errorMessage): void
    {
        if ($success) {
            static::success($successMessage);
        } else {
            static::error($errorMessage);
        }
    }
}

<?php
namespace Simflex\Core\Alert\Console;

use JetBrains\PhpStorm\Deprecated;
use Simflex\Core\Log;

#[Deprecated]
class Alert
{
    public static function text($message)
    {
        Log::notice($message);
    }

    public static function error($message)
    {
        Log::error($message);
    }

    public static function warning($message)
    {
        Log::warning($message);
    }

    public static function success($message)
    {
        Log::info($message);
    }

    public static function result($success, $successMessage, $errorMessage)
    {
        if ($success) {
            static::success($successMessage);
        } else {
            static::error($errorMessage);
        }
    }
}
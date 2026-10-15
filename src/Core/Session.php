<?php

namespace Simflex\Core;

class Session
{
    private static array $data = [];
    private static bool $init = false;

    private static function ensureStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function get(string $key): mixed
    {
        if (!static::$init) {
            static::ensureStarted();
            static::$data = $_SESSION;
            session_write_close();
            static::$init = true;
        }

        return static::$data[$key] ?? null;
    }

    public static function getAll(): array
    {
        if (!static::$init) {
            static::ensureStarted();
            static::$data = $_SESSION;
            session_write_close();
            static::$init = true;
        }

        return static::$data;
    }

    public static function set(string $key, mixed $value): void
    {
        static::ensureStarted();
        if (!static::$init) {
            static::$data = $_SESSION;
            static::$init = true;
        }

        $_SESSION[$key] = static::$data[$key] = $value;
        session_write_close();
    }
}

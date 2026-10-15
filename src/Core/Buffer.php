<?php

namespace Simflex\Core;

class Buffer
{
    protected static array $data = [];

    public static function getOrSet(string $key, callable $callback): mixed
    {
        if (!array_key_exists($key, static::$data)) {
            static::$data[$key] = $callback();
        }
        return static::$data[$key];
    }

    public static function delete(string $key): void
    {
        if (array_key_exists($key, static::$data)) {
            unset(static::$data[$key]);
        }
    }
}

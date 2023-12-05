<?php
namespace Simflex\Core;

class Session
{
    private static $data = [];
    private static $init = false;

    public static function get($key)
    {
        if (!static::$init) {
            session_start();
            static::$data = $_SESSION;
            session_write_close();
            static::$init = true;
        }

        return static::$data[$key] ?? null;
    }

    public static function set($key, $value)
    {
        session_start();
        if (!static::$init) {
            static::$data = $_SESSION;
            static::$init = true;
        }

        $_SESSION[$key] = static::$data[$key] = $value;
        session_write_close();
    }
}
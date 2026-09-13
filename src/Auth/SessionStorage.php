<?php


namespace Simflex\Auth;


class SessionStorage
{

    protected static function ensureStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected static function getKey()
    {
        $prefix = SF_LOCATION_SITE == SF_LOCATION ? 's' : 'a';
        return $prefix . '_user_id';
    }

    public static function get()
    {
        static::ensureStarted();
        return $_SESSION[static::getKey()] ?? null;
    }

    public static function set($userId)
    {
        static::ensureStarted();
        $_SESSION[static::getKey()] = $userId;
    }

}
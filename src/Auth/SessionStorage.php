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

    protected static function getKey(): string
    {
        $prefix = SF_LOCATION_SITE == SF_LOCATION ? 's' : 'a';
        return $prefix . '_user_id';
    }

    public static function get(): mixed
    {
        static::ensureStarted();
        return $_SESSION[static::getKey()] ?? null;
    }

    public static function set($userId): void
    {
        static::ensureStarted();
        $_SESSION[static::getKey()] = $userId;
    }

}
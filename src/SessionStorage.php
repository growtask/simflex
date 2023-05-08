<?php


namespace Simflex\Auth;


class SessionStorage
{

    protected static function getKey()
    {
        $prefix = SF_LOCATION_SITE == SF_LOCATION ? 's' : 'a';
        return $prefix . '_user_id';
    }

    public static function get()
    {
        return $_SESSION[static::getKey()] ?? null;
    }

    public static function set($userId)
    {
        $_SESSION[static::getKey()] = $userId;
    }

}
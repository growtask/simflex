<?php


namespace Simflex\Auth;


use Simflex\Auth\Auth\Chain;
use Simflex\Auth\SignOut\CookieMiddleware;
use Simflex\Auth\SignOut\SessionMiddleware;
use Simflex\Core\Container;
use Simflex\Core\Models\User;
use Simflex\Core\UserInstance;

class Bootstrap
{
    public static function authByMiddlewareChain(Chain $chain)
    {
        $user = $chain->process(null);
        Container::set('user', $user);
        static::initLegacy($user);
    }

    public static function authByUser(User $user)
    {
        Container::set('user', $user);
        static::initLegacy($user);
    }

    protected static function initLegacy(?User $user)
    {
        Container::set('userLegacy', \Simflex\Core\User::class);
        if (SF_LOCATION_ADMIN == SF_LOCATION) {
            $userInstance = new UserInstance('admin_user_id', 'admin_user_hash', 'hash_admin', 'cha', 'csa');
        } else {
            $userInstance = new UserInstance('user_id', 'user_hash', 'hash', 'ch', 'cs');
        }
        if (!empty($user)) {
            $userInstance->initByModel($user);
        }
        \Simflex\Core\User::login($userInstance);
    }

    public static function signOut()
    {
        (new \Simflex\Core\Middleware\Chain([
            new SessionMiddleware(),
            new CookieMiddleware(),
        ]))->process();
        Container::set('user', null);
        //Container::getUserLegacy()::logout2();
    }
}
<?php

namespace Simflex\Auth;

use Simflex\Auth\Auth\Chain;
use Simflex\Auth\SignOut\CookieMiddleware;
use Simflex\Auth\SignOut\SessionMiddleware;
use Simflex\Core\Container;
use Simflex\Models\User;

class Bootstrap
{
    public static function authByMiddlewareChain(Chain $chain): void
    {
        $user = $chain->process(null);
        Container::set('user', $user);
    }

    public static function authByUser(User $user): void
    {
        Container::set('user', $user);
    }

    public static function signOut(): void
    {
        (new \Simflex\Core\Middleware\Chain([
            new SessionMiddleware(),
            new CookieMiddleware(),
        ]))->process();
        Container::set('user', null);
    }
}

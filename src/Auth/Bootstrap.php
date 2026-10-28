<?php

namespace Simflex\Core\Auth;

use Simflex\Core\Auth\Auth\Chain;
use Simflex\Core\Auth\SignOut\CookieMiddleware;
use Simflex\Core\Auth\SignOut\SessionMiddleware;
use Simflex\Core\System\Container;
use Simflex\Core\Models\User;

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
        (new \Simflex\Core\System\Middleware\Chain([
            new SessionMiddleware(),
            new CookieMiddleware(),
        ]))->process();
        Container::set('user', null);
    }
}

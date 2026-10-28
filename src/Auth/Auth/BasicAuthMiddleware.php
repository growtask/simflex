<?php

namespace Simflex\Core\Auth\Auth;

use Simflex\Core\System\Container;

class BasicAuthMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function handle(mixed $payload, \Closure $next): mixed
    {
        if ($payload) {
            return $next($payload);
        }
        $request = Container::getRequest();
        $login = $request?->server('PHP_AUTH_USER');
        $pass = $request?->server('PHP_AUTH_PW');
        if ($login && $pass) {
            $user = Container::getFactory()->getStatic($this->userModelClass)::findOne(['login' => $login, 'password' => md5($pass)]);
            if ($user) {
                return $next($user);
            }
        }
        return $next($payload);
    }
}

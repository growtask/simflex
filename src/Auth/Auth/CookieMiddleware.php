<?php

namespace Simflex\Core\Auth\Auth;

use Simflex\Core\Auth\CookieTokenBag;
use Simflex\Core\System\Container;
use Simflex\Core\Models\User;
use Simflex\Core\Auth\Models\UserAuth;

class CookieMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function handle(mixed $payload, \Closure $next): mixed
    {
        if ($payload) {
            return $next($payload);
        }
        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
        $token = $cookies->get();
        if ($token) {
            $modelAuth = UserAuth::findByToken($token);
            if ($modelAuth) {
                /** @var User $user */
                $user = Container::getFactory()->create($this->userModelClass, $modelAuth['user_id']);
                if ($user->getId()) {
                    $cookies->prolong();
                    return $next($user);
                }
            }
        }
        return $next($payload);
    }
}

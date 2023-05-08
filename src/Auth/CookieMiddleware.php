<?php

namespace Simflex\Auth\Auth;

use Simflex\Auth\CookieTokenBag;
use Simflex\Core\Models\User;
use Simflex\Auth\Models\UserAuth;

class CookieMiddleware extends BaseMiddleware
{

    /**
     * @inheritDoc
     */
    public function handle($payload, \Closure $next)
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
                $user = new $this->userModelClass($modelAuth['user_id']);
                if ($user->getId()) {
                    $cookies->prolong();
                    return $next($user);
                }
            }
        }
        return $next($payload);
    }
}
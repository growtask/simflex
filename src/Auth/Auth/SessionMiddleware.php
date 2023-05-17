<?php

namespace Simflex\Auth\Auth;

use Simflex\Auth\SessionStorage;
use Simflex\Core\Models\User;

class SessionMiddleware extends BaseMiddleware
{

    /**
     * @inheritDoc
     */
    public function handle($payload, \Closure $next)
    {
        if ($payload) {
            return $next($payload);
        }
        $userId = SessionStorage::get();
        if ($userId) {
            /** @var User $user */
            $user = new $this->userModelClass($userId);
            if ($user->getId()) {
                return $next($user);
            }
        }
        return $next($payload);
    }
}
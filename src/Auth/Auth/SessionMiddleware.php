<?php

namespace Simflex\Core\Auth\Auth;

use Simflex\Core\Auth\SessionStorage;
use Simflex\Core\System\Container;
use Simflex\Core\Models\User;

class SessionMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function handle(mixed $payload, \Closure $next): mixed
    {
        if ($payload) {
            return $next($payload);
        }
        $userId = SessionStorage::get();
        if ($userId) {
            /** @var User $user */
            $user = Container::getFactory()->create($this->userModelClass, $userId);
            if ($user->getId()) {
                return $next($user);
            }
        }
        return $next($payload);
    }
}

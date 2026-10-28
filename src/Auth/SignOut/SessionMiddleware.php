<?php

namespace Simflex\Core\Auth\SignOut;

use Simflex\Core\Auth\SessionStorage;
use Simflex\Core\System\Middleware\Handler;

class SessionMiddleware implements Handler
{
    /**
     * @inheritDoc
     */
    public function handle(mixed $payload, \Closure $next): mixed
    {
        SessionStorage::set(null);
        return $next($payload);
    }
}

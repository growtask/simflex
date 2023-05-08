<?php

namespace Simflex\Auth\SignOut;

use Simflex\Auth\SessionStorage;
use Simflex\Core\Middleware\Handler;

class SessionMiddleware implements Handler
{

    /**
     * @inheritDoc
     */
    public function handle($payload, \Closure $next)
    {
        SessionStorage::set(null);
        return $next($payload);
    }
}
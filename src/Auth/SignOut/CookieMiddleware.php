<?php

namespace Simflex\Auth\SignOut;

use Simflex\Auth\CookieTokenBag;
use Simflex\Core\Middleware\Handler;

class CookieMiddleware implements Handler
{

    /**
     * @inheritDoc
     */
    public function handle(mixed $payload, \Closure $next): mixed
    {
        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
        $cookies->delete();
        return $next($payload);
    }
}
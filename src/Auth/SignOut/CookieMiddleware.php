<?php

namespace Simflex\Core\Auth\SignOut;

use Simflex\Core\Auth\CookieTokenBag;
use Simflex\Core\System\Middleware\Handler;

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

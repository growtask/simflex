<?php

namespace Simflex\Auth\SignOut;

use Simflex\Auth\CookieTokenBag;
use Simflex\Core\Middleware\Handler;

class CookieMiddleware implements Handler
{

    /**
     * @inheritDoc
     */
    public function handle($payload, \Closure $next)
    {
        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
        $cookies->delete();
        return $next($payload);
    }
}
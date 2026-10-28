<?php

namespace Simflex\Core\System\Middleware;

class GagHandler implements Handler
{
    public function handle(mixed $payload, \Closure $next): mixed
    {
        return $payload;
    }
}

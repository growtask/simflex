<?php

namespace Simflex\Core\Middleware;

class GagHandler implements Handler
{
    public function handle(mixed $payload, \Closure $next): mixed
    {
        return $payload;
    }
}

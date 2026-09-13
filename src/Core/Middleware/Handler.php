<?php

namespace Simflex\Core\Middleware;

interface Handler
{
    /**
     * @param mixed $payload any data
     * @return mixed handled payload
     */
    public function handle(mixed $payload, \Closure $next): mixed;
}

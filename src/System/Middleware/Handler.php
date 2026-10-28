<?php

namespace Simflex\Core\System\Middleware;

interface Handler
{
    /**
     * @param mixed $payload любые данные
     * @return mixed обработанные данные
     */
    public function handle(mixed $payload, \Closure $next): mixed;
}

<?php


namespace Simflex\Core\Middleware;


use Simflex\Core\Middleware\Handler;

class GagHandler implements Handler
{

    public function handle($payload, Handler $next)
    {
        return $payload;
    }
}
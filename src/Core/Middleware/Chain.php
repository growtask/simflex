<?php


namespace Simflex\Core\Middleware;


use Simflex\Core\Middleware\Handler;

class Chain implements Handler
{

    /** @var Handler[] */
    protected $middlewares = [];

    /**
     * Chain constructor.
     * @param Handler[] $middlewares
     */
    public function __construct($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function handle($payload, \Closure $next)
    {
        $handler = array_shift($this->middlewares);
        return $handler ? $handler->handle($payload, function ($payload) use ($next) {
            return $this->handle($payload, $next);
        }) : $payload;
    }

    public function process($payload = null)
    {
        return $this->handle($payload, function () use ($payload) {
            return $payload;
        });
    }

}
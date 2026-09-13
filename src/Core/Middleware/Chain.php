<?php

namespace Simflex\Core\Middleware;

class Chain implements Handler
{
    /** @var Handler[] */
    protected array $middlewares = [];

    /**
     * Chain constructor.
     * @param Handler[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function handle(mixed $payload, \Closure $next): mixed
    {
        $handler = array_shift($this->middlewares);
        return $handler ? $handler->handle($payload, function ($payload) use ($next) {
            return $this->handle($payload, $next);
        }) : $payload;
    }

    public function process(mixed $payload = null): mixed
    {
        return $this->handle($payload, function () use ($payload) {
            return $payload;
        });
    }
}

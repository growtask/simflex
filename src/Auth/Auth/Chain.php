<?php

namespace Simflex\Auth\Auth;

class Chain extends \Simflex\Core\Middleware\Chain
{
    public function process(mixed $payload = null): mixed
    {
        foreach ($this->middlewares as $mw) {
            if (!$mw instanceof BaseMiddleware) {
                throw new \InvalidArgumentException("All middlewares must be sublass of Simflex\Auth\Auth\BaseMiddleware");
            }
        }
        return parent::process($payload);
    }
}

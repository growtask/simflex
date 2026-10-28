<?php

namespace Simflex\Core\Auth\Auth;

class Chain extends \Simflex\Core\System\Middleware\Chain
{
    public function process(mixed $payload = null): mixed
    {
        foreach ($this->middlewares as $mw) {
            if (!$mw instanceof BaseMiddleware) {
                throw new \InvalidArgumentException("All middlewares must be sublass of Simflex\Core\Auth\Auth\BaseMiddleware");
            }
        }
        return parent::process($payload);
    }
}

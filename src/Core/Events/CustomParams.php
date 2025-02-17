<?php

namespace Simflex\Core\Events;

class CustomParams
{
    public array $params = [];

    public function __construct(...$params)
    {
        $this->params = $params;
    }
}
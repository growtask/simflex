<?php

namespace Simflex\Core\System\Events;

class CustomParams
{
    public array $params = [];

    public function __construct(...$params)
    {
        $this->params = $params;
    }
}

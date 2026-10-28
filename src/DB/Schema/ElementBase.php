<?php

namespace Simflex\DB\Schema;

abstract class ElementBase implements Element
{
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }
}

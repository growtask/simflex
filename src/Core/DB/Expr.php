<?php

namespace Simflex\Core\DB;

/**
 * Class Expr
 * Use instance of this class to unescape your statement
 */
class Expr
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

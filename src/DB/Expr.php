<?php

namespace Simflex\DB;

/**
 * Класс Expr
 * Экземпляр этого класса вставляет выражение в запрос без экранирования
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

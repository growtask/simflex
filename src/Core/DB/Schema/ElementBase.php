<?php
namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB\Schema\Element;

abstract class ElementBase implements Element
{
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }
}

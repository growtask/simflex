<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Structure\Data\TableDefinition;

abstract class Table
{
    abstract public function name(): string;

    abstract public function definition(): TableDefinition;
}

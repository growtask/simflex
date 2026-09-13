<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Structure\Data\TableDefinition;

abstract class Table
{
    abstract public static function name(): string;

    abstract public static function definition(): TableDefinition;
}

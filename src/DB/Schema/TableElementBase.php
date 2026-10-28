<?php

namespace Simflex\DB\Schema;

abstract class TableElementBase extends ElementBase
{
    protected ?Table $table = null;

    public function getTable(): ?Table
    {
        return $this->table;
    }
}

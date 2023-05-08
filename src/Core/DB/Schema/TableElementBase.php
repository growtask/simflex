<?php
namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB\Schema\ElementBase;
use Simflex\Core\DB\Schema\Table;

abstract class TableElementBase extends ElementBase
{
    /** @var ?\Simflex\Core\DB\Schema\Table */
    protected $table = null;

    public function getTable(): ?Table
    {
        return $this->table;
    }
}
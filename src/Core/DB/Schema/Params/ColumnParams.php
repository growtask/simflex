<?php

namespace Simflex\Core\DB\Schema\Params;

class ColumnParams
{
    public $isNull = true;
    public $default = null;
    public $autoIncrement = false;
    public $comment = '';
    public $collate = '';
    public $isPrimaryKey = false;

    public function compare(ColumnParams $other): bool
    {
        return
            $this->isNull == $other->isNull &&
            $this->default == $other->default &&
            $this->autoIncrement == $other->autoIncrement &&
            $this->comment == $other->comment &&
            $this->collate == $other->collate &&
            $this->isPrimaryKey == $other->isPrimaryKey;
    }
}
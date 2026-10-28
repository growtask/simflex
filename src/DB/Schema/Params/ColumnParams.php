<?php

namespace Simflex\Core\DB\Schema\Params;

class ColumnParams
{
    public bool $isNull = true;
    public string|int|float|bool|null $default = null;
    public bool $defaultSet = false;
    public bool $autoIncrement = false;
    public string $comment = '';
    public string $collate = '';
    public bool $isPrimaryKey = false;

    public function compare(ColumnParams $other): bool
    {
        return
            $this->isNull === $other->isNull &&
            $this->default == $other->default &&
            $this->defaultSet === $other->defaultSet &&
            $this->autoIncrement === $other->autoIncrement &&
            $this->comment === $other->comment &&
            $this->collate === $other->collate &&
            $this->isPrimaryKey === $other->isPrimaryKey;
    }
}

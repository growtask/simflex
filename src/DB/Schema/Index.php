<?php

namespace Simflex\DB\Schema;

use Simflex\DB\DB;
use Simflex\DB\Schema\Params\IndexParams;

class Index extends ElementBase
{
    protected IndexParams $params;

    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->params = new IndexParams();
    }

    /**
     * Задаёт индекс
     */
    public function index(array|string $column): static
    {
        if (!is_array($column)) {
            $column = [$column];
        }

        if (!$this->name) {
            $this->name = crc32(microtime()) . '_' . $column[0] . '_idx';
        }

        $this->params->isKey = false;
        $this->params->keyParts = $column;
        return $this;
    }

    /**
     * Задаёт ключ
     */
    public function key(array|string $column): static
    {
        $this->index($column);
        $this->params->isKey = true;
        return $this;
    }

    public function fullText(): static
    {
        $this->params->type = IndexParams::TYPE_FULLTEXT;
        return $this;
    }

    public function spatial(): static
    {
        $this->params->type = IndexParams::TYPE_SPATIAL;
        return $this;
    }

    public function toString(): string
    {
        if (!$this->name) {
            throw new \Exception('Index should have a symbol name');
        }

        $sql = $this->params->type . ' ';
        $sql .= $this->params->isKey ? 'KEY ' : 'INDEX ';
        $sql .= DB::wrapName($this->name) . ' ';
        $sql .= '(' . implode(',', $this->params->keyParts) . ')';

        return $sql;
    }
}

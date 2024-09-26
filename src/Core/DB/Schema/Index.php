<?php

namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB;
use Simflex\Core\DB\Schema\Params\IndexParams;

class Index extends ElementBase
{
    protected IndexParams $params;

    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->params = new IndexParams();
    }

    /**
     * Sets index
     * @param array|string $column
     * @return $this
     */
    public function index($column): self
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
     * Sets key
     * @param array|string $column
     * @return $this
     */
    public function key($column): self
    {
        $this->index($column);
        $this->params->isKey = true;
        return $this;
    }

    public function fullText(): self
    {
        $this->params->type = IndexParams::TYPE_FULLTEXT;
        return $this;
    }

    public function spatial(): self
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
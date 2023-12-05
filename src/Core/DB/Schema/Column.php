<?php

namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB;
use Simflex\Core\DB\Schema\Params\ColumnParams;
use Simflex\Core\DB\Schema\Table;
use Simflex\Core\DB\Schema\TableElementBase;

class Column extends TableElementBase
{
    /** @var \Simflex\Core\DB\Schema\Params\ColumnParams */
    protected $params;
    protected $type = '';

    public const TYPE_INT = 'int';

    public const TYPE_TINYINT = 'tinyint';
    public const TYPE_VARCHAR = 'varchar';
    public const TYPE_TEXT = 'text';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_ENUM = 'enum';

    public function __construct(string $name, ?Table $table = null)
    {
        $this->name = $name;
        $this->params = new ColumnParams();
        $this->table = $table;
    }

    public function compare(Column $other): bool
    {
        return $this->params->compare($other->params) && $this->name == $other->name && $this->type == $other->type;
    }

    // ------------ BUILDER ------------ //

    /**
     * Set data type
     *
     * @param string $type Type name
     * @param int|null $length Type length
     * @return $this
     */
    public function dataType(string $type, ?int $length = null): self
    {
        $this->type = $type;
        if ($length !== null) {
            $this->type .= '(' . $length . ')';
        }

        return $this;
    }

    public function setNull(bool $isNull = true): self
    {
        $this->params->isNull = $isNull;
        return $this;
    }

    public function setDefault($default): self
    {
        $this->params->default = $default;
        $this->params->defaultSet = true;
        return $this;
    }

    public function autoIncrement(bool $v = true): self
    {
        $this->params->autoIncrement = $v;
        $this->params->isNull = false;
        return $this;
    }

    public function comment(string $comment): self
    {
        $this->params->comment = $comment;
        return $this;
    }

    public function collate(string $collation): self
    {
        $this->params->collate = $collation;
        return $this;
    }

    public function primaryKey(): self
    {
        $this->params->isPrimaryKey = true;
        return $this;
    }

    public function foreignKey(string $table, $other = '')
    {
        $this->table->addConstraint()->foreignKey(
            $table,
            is_array($other) ? $other : [$this->name => $other ?: $this->name]
        );
        return $this;
    }

    // ------------ UTIL ------------ //

    /**
     * Returns params
     *
     * @return \Simflex\Core\DB\Schema\Params\ColumnParams
     */
    public function getParams(): ColumnParams
    {
        return $this->params;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function toString(): string
    {
        if (!$this->name) {
            throw new \Exception("Invalid column name");
        }

        if (!$this->type) {
            throw new \Exception("Invalid data type");
        }

        $sql = DB::wrapName($this->name) . ' ' . $this->type . ' ';
        $sql .= $this->params->isNull ? 'NULL ' : 'NOT NULL ';

        if ($this->params->defaultSet) {
            $sql .= 'DEFAULT '
                . (is_string($this->params->default) ? DB::wrapString($this->params->default) : $this->params->default);
        }

        if ($this->params->isPrimaryKey) {
            $sql .= 'PRIMARY KEY ';
        }

        if ($this->params->autoIncrement) {
            $sql .= 'AUTO_INCREMENT ';
        }

        if ($this->params->comment) {
            $sql .= 'COMMENT ' . DB::wrapString($this->params->comment);
        }

        if ($this->params->collate) {
            $sql .= 'COLLATE ' . $this->params->collate;
        }

        return $sql;
    }
}
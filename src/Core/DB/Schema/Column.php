<?php

namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB;
use Simflex\Core\DB\Schema\Params\ColumnParams;
use Simflex\Core\DB\Schema\Table;
use Simflex\Core\DB\Schema\TableElementBase;

class Column extends TableElementBase
{
    protected ColumnParams $params;
    protected string $type = '';

    public const TYPE_INT = 'int';
    public const TYPE_TINYINT = 'tinyint';
    public const TYPE_VARCHAR = 'varchar';
    public const TYPE_TEXT = 'text';
    public const TYPE_LONGTEXT = 'longtext';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_ENUM = 'enum';
    public const TYPE_DATE = 'date';
    public const TYPE_DATE_TIME = 'datetime';
    public const TYPE_TIMESTAMP = 'timestamp';

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
     */
    public function dataType(string $type, ?int $length = null): static
    {
        $this->type = $type;
        if ($length !== null) {
            $this->type .= '(' . $length . ')';
        }

        return $this;
    }

    public function setNull(bool $isNull = true): static
    {
        $this->params->isNull = $isNull;
        return $this;
    }

    public function setDefault(string|int|float|bool|null $default): static
    {
        $this->params->default = $default;
        $this->params->defaultSet = true;
        return $this;
    }

    public function autoIncrement(bool $v = true): static
    {
        $this->params->autoIncrement = $v;
        $this->params->isNull = false;
        return $this;
    }

    public function comment(string $comment): static
    {
        $this->params->comment = $comment;
        return $this;
    }

    public function collate(string $collation): static
    {
        $this->params->collate = $collation;
        return $this;
    }

    public function primaryKey(): static
    {
        $this->params->isPrimaryKey = true;
        return $this;
    }

    public function foreignKey(string $table, string|array $other = ''): static
    {
        $this->table->addConstraint()->foreignKey(
            $table,
            is_array($other) ? $other : [$this->name => $other ?: $this->name]
        );
        return $this;
    }

    public function unique(bool $isIndex = false): static
    {
        $this->table->addConstraint()->unique($this->name, $isIndex);
        return $this;
    }

    public function index(): static
    {
        $this->table->addIndex()->index($this->name);
        return $this;
    }

    public function key(): static
    {
        $this->table->addIndex()->key($this->name);
        return $this;
    }

    // ------------ UTIL ------------ //

    /**
     * Returns params
     */
    public function getParams(): ColumnParams
    {
        return $this->params;
    }

    protected function prepareDefault(): string
    {
        if (is_string($this->params->default)) {
            if (in_array($this->type, [self::TYPE_TIMESTAMP, self::TYPE_DATE, self::TYPE_DATE_TIME])) {
                return $this->params->default;
            }

            return DB::wrapString($this->params->default);
        }

        if (is_null($this->params->default)) {
            return 'NULL';
        }

        if (is_bool($this->params->default)) {
            return $this->params->default ? '1' : '0';
        }

        return (string)$this->params->default;
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
            $sql .= 'DEFAULT ' . $this->prepareDefault() . ' ';
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

<?php

namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB;
use Simflex\Core\DB\Schema\ElementBase;
use Simflex\Core\DB\Schema\Params\ConstraintParams;

class Constraint extends ElementBase
{
    protected ConstraintParams $params;

    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->params = new ConstraintParams();
    }

    // ------------ BUILDER ------------ //

    /**
     * Sets primary key constraint
     *
     * @param array|string $column Column(s) to index
     */
    public function primaryKey(array|string $column): static
    {
        if (!is_array($column)) {
            $column = [$column];
        }

        if (!$this->name) {
            $this->name = crc32(microtime()) . '_' . $column[0] . '_pk';
        }

        $this->params->isUnique = $this->params->isForeign = false;
        $this->params->isPrimary = true;
        $this->params->keyParts = $column;

        return $this;
    }

    /**
     * Sets unique key/index constraint
     *
     * @param array|string $column Column(s) to index
     * @param bool $isIndex Should mark this constraint as index
     */
    public function unique(array|string $column, bool $isIndex = false): static
    {
        if (!is_array($column)) {
            $column = [$column];
        }

        if (!$this->name) {
            $this->name = crc32(microtime()) . '_' . $column[0] . '_uq';
        }

        $this->params->isPrimary = $this->params->isForeign = false;
        $this->params->isUnique = true;
        $this->params->keyParts = $column;
        $this->params->isUniqueIndex = $isIndex;

        return $this;
    }

    /**
     * References column(s) in another table
     *
     * @param string $referenceTable Target reference table
     * @param string|array $colToReference Associative array or string of my_column => their_column
     */
    public function foreignKey(string $referenceTable, string|array $colToReference): static
    {
        $columns = [];
        $referenceColumns = [];

        if (is_array($colToReference)) {
            foreach ($colToReference as $column => $reference) {
                $columns[] = $column;
                $referenceColumns[] = $reference;
            }
        } else {
            $columns = $referenceColumns = [$colToReference];
        }

        if (!$this->name) {
            $this->name = crc32(microtime()) . '_' . $columns[0] . '_' . $referenceColumns[0] . '_fk';
        }

        $this->params->isUnique = $this->params->isPrimary = false;
        $this->params->isForeign = true;
        $this->params->reference = $referenceTable;
        $this->params->keyParts = $columns;
        $this->params->referenceColumns = $referenceColumns;

        return $this;
    }

    /**
     * Sets MATCH option for foreign key
     *
     * @param string $match ConstraintParams::REFERENCE_MATCH_{X}
     */
    public function match(string $match): static
    {
        $this->params->match = $match;
        return $this;
    }

    /**
     * Sets ON DELETE option for foreign key
     *
     * @param string $option ConstraintParams::REFERENCE_OPTION_{X}
     */
    public function onDelete(string $option): static
    {
        $this->params->onDelete = $option;
        return $this;
    }

    /**
     * Sets ON UPDATE option for foreign key
     *
     * @param string $option ConstraintParams::REFERENCE_OPTION_{X}
     */
    public function onUpdate(string $option): static
    {
        $this->params->onUpdate = $option;
        return $this;
    }

    // ------------ UTIL ------------ //

    public function getParams(): ConstraintParams
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
            throw new \Exception('Constraint should have a symbol name');
        }

        if (!$this->params->isForeign && !$this->params->isPrimary && !$this->params->isUnique) {
            throw new \Exception('Unknown constraint type');
        }

        $sql = 'CONSTRAINT ' . $this->name . ' ';

        if ($this->params->isForeign) {
            $sql .= 'FOREIGN KEY ';
        } elseif ($this->params->isPrimary) {
            $sql .= 'PRIMARY KEY ';
        } elseif ($this->params->isUnique) {
            $sql .= 'UNIQUE ' . ($this->params->isUniqueIndex ? 'INDEX ' : 'KEY ');
        }

        $sql .= '(' . implode(',', $this->params->keyParts) . ') ';

        if ($this->params->isForeign) {
            $sql .= 'REFERENCES ' . DB::wrapName($this->params->reference) . ' ';
            $sql .= '(' . implode(',', $this->params->referenceColumns) . ') ';

            if ($this->params->match) {
                $sql .= 'MATCH ' . $this->params->match . ' ';
            }

            if ($this->params->onDelete) {
                $sql .= 'ON DELETE ' . $this->params->onDelete . ' ';
            }

            if ($this->params->onUpdate) {
                $sql .= 'ON UPDATE ' . $this->params->onUpdate . ' ';
            }
        }

        return $sql;
    }
}

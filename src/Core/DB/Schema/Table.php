<?php

namespace Simflex\Core\DB\Schema;

use Simflex\Core\DB;
use Simflex\Core\DB\Schema\Constraint;
use Simflex\Core\DB\Schema\ElementBase;
use Simflex\Core\DB\Schema\Params\TableParams;
use Simflex\Core\DB\Schema\Column;

class Table extends ElementBase
{
    /** @var \Simflex\Core\DB\Schema\Params\TableParams */
    protected $params;

    /** @var \Simflex\Core\DB\Schema\Params\TableParams */
    protected $oldParams;

    /** @var \Simflex\Core\DB\Schema\Element[] */
    protected $elements = [];

    /** @var \Simflex\Core\DB\Schema\Element[] */
    protected $oldElements = [];

    protected $isExisting;
    protected $dropQueue = [];

    public function __construct(string $name, bool $isExisting = false)
    {
        $this->params = new TableParams();
        $this->name = $name;
        $this->isExisting = $isExisting;

        if ($isExisting) {
            $this->oldParams = $this->params;
            $this->oldElements = $this->elements;
        }
    }

    // ------------ BUILDER ------------ //

    /**
     * Create table only if one doesn't exist yet
     */
    public function ifNotExists(): self
    {
        $this->params->ifNotExists = true;
        return $this;
    }

    /**
     * Create temporary table
     */
    public function temporary(): self
    {
        $this->params->temporary = true;
        return $this;
    }

    /**
     * Set auto increment counter
     *
     * @param int $n
     * @return $this
     */
    public function autoIncrement(int $n): self
    {
        $this->params->autoIncrement = $n;
        return $this;
    }

    /**
     * Set table comment
     *
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        $this->params->comment = $comment;
        return $this;
    }

    /**
     * Set table character set
     *
     * @param string $charset Character set
     * @param bool $isDefault DEFAULT modifier
     * @return $this
     */
    public function characterSet(string $charset, bool $isDefault = false): self
    {
        $this->params->characterSet = $charset;
        $this->params->defaultCharacterSet = $isDefault;

        return $this;
    }

    /**
     * Adds a column
     *
     * @param string $name Column name
     * @param string $type Column type
     * @param int|null $length Column type length
     * @return \Simflex\Core\DB\Schema\Column
     */
    public function addColumn(string $name, string $type, ?int $length = null): Column
    {
        $col = new Column($name);
        $col->dataType($type, $length);

        $this->elements[$name] = $col;
        return $col;
    }

    /**
     * Adds a constraint
     *
     * @param string $name Constraint symbol name
     * @return \Simflex\Core\DB\Schema\Constraint
     */
    public function addConstraint(string $name): Constraint
    {
        $const = new Constraint($name);

        $this->elements[$name] = $const;
        return $const;
    }

    /**
     * Drops a column
     *
     * @param string|array $name Column name(s)
     * @return $this
     */
    public function dropColumns($name): Table
    {
        if (is_array($name)) {
            $this->dropQueue = array_unique(array_merge($this->dropQueue, $name));
        } else {
            $this->dropQueue[] = $name;
        }

        return $this;
    }

    // ------------ HELPERS ------------ //

    public function integer(string $name, ?int $length = null): Column
    {
        return $this->addColumn($name, Column::TYPE_INT, $length);
    }

    public function string(string $name, ?int $length = 255): Column
    {
        return $this->addColumn($name, Column::TYPE_VARCHAR, $length);
    }

    public function text(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_TEXT);
    }

    public function boolean(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_TINYINT, 1);
    }

    // ------------ UTIL ------------ //

    /**
     * Returns params
     *
     * @return \Simflex\Core\DB\Schema\Params\TableParams
     */
    public function getParams(): TableParams
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
            throw new \Exception('Invalid table name');
        }

        if (!$this->elements) {
            throw new \Exception('Table cannot be empty');
        }

        return $this->isExisting ? $this->alterToString() : $this->createToString();
    }

    private function alterToString(): string
    {
        $sql = 'ALTER TABLE ' . DB::wrapName($this->name) . ' ';
        $firstCmd = true;

        // drop columns
        foreach ($this->dropQueue as $col) {
            if (!$firstCmd) {
                $sql .= ', ';
            }

            $sql .= 'DROP COLUMN ' . DB::wrapName($col);
            $firstCmd = false;
        }

        /** @var \Simflex\Core\DB\Schema\Element[] $toMod */
        $toMod = [];

        /** @var \Simflex\Core\DB\Schema\Element[] $toMod */
        $toAdd = [];

        foreach ($this->params as $p) {
            if (!($p instanceof Column)) {
                continue;
            }

            $found = false;
            foreach ($this->oldParams as $op) {
                if (!($op instanceof Column)) {
                    continue;
                }

                if ($p->getName() == $op->getName()) {
                    $found = true;
                    if (!$p->compare($op)) {
                        $toMod[] = $p;
                    }

                    break;
                }
            }

            if (!$found) {
                $toAdd[] = $p;
            }
        }

        // modify old ones
        foreach ($toMod as $col) {
            if (!$firstCmd) {
                $sql .= ', ';
            }

            $sql .= 'MODIFY COLUMN ' . $col->toString();
            $firstCmd = false;
        }

        // add new ones
        foreach ($toAdd as $col) {
            if (!$firstCmd) {
                $sql .= ', ';
            }

            $sql .= 'ADD COLUMN ' . $col->toString();
            $firstCmd = false;
        }

        return $sql;
    }

    private function createToString(): string
    {
        $sql = 'CREATE ';

        if ($this->params->temporary) {
            $sql .= 'TEMPORARY ';
        }

        $sql .= 'TABLE ';

        if ($this->params->ifNotExists) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= DB::wrapName($this->name) . ' (';

        $builtElements = [];
        foreach ($this->elements as $element) {
            $builtElements[] = $element->toString();
        }

        $sql .= implode(',', $builtElements) . ') ';

        if ($this->params->autoIncrement) {
            $sql .= 'AUTO_INCREMENT ' . $this->params->autoIncrement . ' ';
        }

        if ($this->params->comment) {
            $sql .= 'COMMENT ' . DB::wrapString($this->params->comment) . ' ';
        }

        if ($this->params->collate) {
            $sql .= 'COLLATE ' . $this->params->collate . ' ';
        }

        if ($this->params->characterSet) {
            $sql .= 'CHARSET ' . $this->params->characterSet . ' ';
        }

        return $sql;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
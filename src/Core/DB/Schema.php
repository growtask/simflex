<?php

namespace Simflex\Core\DB;

use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\Schema\Table;

class Schema
{
    /** @var \Simflex\Core\DB\Schema\Table[] Existing tables */
    protected $tables = [];

    /** @var \Simflex\Core\DB\Schema\Table[] Tables that await their creation (commit) */
    protected $awaitingCreate = [];

    /** @var \Simflex\Core\DB\Schema\Table[] Tables that await their deletion */
    protected $awaitingDelete = [];

    /** @var \Simflex\Core\DB\Schema\Table[] Tables that have been created while this object is alive */
    protected $sessionCreated = [];

    protected $awaitingAlter = [];

    public function __construct()
    {
        $this->reload();
    }

    // ------------ UTIL ------------ //
    public function reload()
    {
        $dbName = Container::getConfig()::$db_name;
        $this->tables = [];

        $tables = DB::query(
            'select TABLE_NAME, AUTO_INCREMENT from information_schema.TABLES where TABLE_SCHEMA = ?',
            [$dbName]
        );
        while ($table = DB::fetch($tables)) {
            $tableName = $table['TABLE_NAME'];
            $tab = $this->tables[$tableName] = $this->loadTable($tableName);
            if ($table['AUTO_INCREMENT']) {
                $tab->autoIncrement($table['AUTO_INCREMENT']);
            }
        }
    }

    protected function loadTable(string $name): Table
    {
        $table = new Table($name, true);

        $columns = DB::query('select * from information_schema.COLUMNS where TABLE_NAME = ? and TABLE_SCHEMA = ?', [
            $name,
            Container::getConfig()::$db_name
        ]);

        while ($col = DB::fetch($columns)) {
            $c = $table->addColumn(
                $col['COLUMN_NAME'],
                $col['DATA_TYPE'],
                $col['CHARACTER_MAXIMUM_LENGTH'] ?? $col['NUMERIC_PRECISION'] + 1
            );

            if ($col['COLUMN_DEFAULT']) {
                $c->setDefault($col['COLUMN_DEFAULT']);
            }

            if ($col['COLUMN_KEY'] == 'PRI') {
                $c->primaryKey();
            }

            if (strpos($col['EXTRA'], 'auto_increment') !== false) {
                $c->autoIncrement();
            }

            $c->comment($col['COLUMN_COMMENT']);
            $c->setNull($col['IS_NULLABLE'] == 'YES');
        }

        $table->keepParams();

        // TODO: load constraints
        return $table;
    }

    public function hasTable(string $name): bool
    {
        return isset($this->tables[$name]);
    }

    // ------------ ALTERNATION ------------ //

    /**
     * @param string $name Table name
     * @param callable $fn Table data callback (Schema\Table passed as argument)
     */
    public function createTable(string $name, callable $fn)
    {
        $table = new Table($name);
        $fn($table);

        $this->awaitingCreate[] = $table;
    }

    /**
     * Drops table
     *
     * @throws \Exception
     */
    public function dropTable(string $name, bool $checkIfExists = false)
    {
        if ($checkIfExists && !$this->hasTable($name)) {
            throw new \Exception('Table ' . $name . ' does not exist');
        }

        $this->awaitingDelete[] = $this->tables[$name]  ?? $this->sessionCreated[$name];
    }

    public function table(string $name, callable $fn)
    {
        if (!$this->hasTable($name)) {
            $this->createTable($name, $fn);
        } else {
            $fn($this->tables[$name]);
            $this->awaitingCreate[] = $this->tables[$name];
            $this->awaitingAlter[] = $name;
        }
    }

    // ------------ EXECUTION ------------ //

    /**
     * Commits changes
     *
     * @return bool
     * @throws \Exception
     */
    public function commit(): bool
    {
        return $this->commitDeletion() && $this->commitCreation();
    }

    /**
     * Rollbacks created tables
     *
     * @return bool
     * @throws \Exception
     */
    public function rollback(): bool
    {
        foreach ($this->sessionCreated as $table) {
            $this->dropTable($table->getName());
        }

        return $this->commitDeletion();
    }

    protected function commitDeletion(): bool
    {
        // delete tables, no rollback available
        foreach ($this->awaitingDelete as $table) {
            $status = !!DB::query('DROP TABLE IF EXISTS `' . $table->getName() . '`');
            if (!$status) {
                return false;
            }
        }

        // clear pool
        $this->awaitingDelete = [];
        return true;
    }

    /**
     * Commits creation changes
     *
     * @return bool
     * @throws \Exception
     */
    protected function commitCreation(): bool
    {
        foreach ($this->awaitingCreate as $table) {
            // try creating the table
            $status = !!DB::query($table->toString());
            if (!$status) {
                return false;
            }

            // if everything went smooth, let's check if that table was already made
            if (!in_array($table->getName(), $this->awaitingAlter)) {
                $params = $table->getParams();
                if (!$params->ifNotExists || $params->ifNotExists && !$this->hasTable($table->getName())) {
                    $this->sessionCreated[$table->getName()] = $table;
                }
            }
        }

        // clear pools
        $this->awaitingCreate = [];
        $this->awaitingAlter = [];
        return true;
    }
}
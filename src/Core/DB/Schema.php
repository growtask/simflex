<?php

namespace Simflex\Core\DB;

use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Log;
use Simflex\Core\DB\Schema\Table;

class Schema
{
    /** @var Table[] Existing tables */
    protected array $tables = [];

    /** @var Table[] Tables that await their creation (commit) */
    protected array $awaitingCreate = [];

    /** @var Table[] Tables that await their deletion */
    protected array $awaitingDelete = [];

    /** @var Table[] Tables that have been created while this object is alive */
    protected array $sessionCreated = [];

    protected array $awaitingAlter = [];

    /** @var callable[] Data to write once the tables are created, see seed() */
    protected array $awaitingSeed = [];

    public function __construct()
    {
        $this->reload();
    }

    // ------------ UTIL ------------ //
    public function reload(): void
    {
        $dbName = Container::getConfig()->db['name'];
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
            Container::getConfig()->db['name'],
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
    public function createTable(string $name, callable $fn): void
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
    public function dropTable(string $name, bool $checkIfExists = false): void
    {
        if ($checkIfExists && !$this->hasTable($name)) {
            throw new \Exception('Table ' . $name . ' does not exist');
        }

        $this->awaitingDelete[] = $this->tables[$name] ?? $this->sessionCreated[$name];
    }

    /**
     * Writes initial data once the tables of the migration are created (on commit), e.g. the rows of a new table:
     * $s->createTable('menu', ...); $s->seed(fn () => Menu::bulkInsert([...]));
     * An exception thrown by the callback or false returned fails the commit, the migration is rolled back with its tables.
     */
    public function seed(callable $fn): void
    {
        $this->awaitingSeed[] = $fn;
    }

    public function table(string $name, callable $fn): void
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
     * @throws \Exception
     */
    public function commit(): bool
    {
        return $this->commitDeletion() && $this->commitCreation() && $this->commitSeed();
    }

    /**
     * Rollbacks created tables
     *
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

    protected function commitSeed(): bool
    {
        $seeds = $this->awaitingSeed;
        $this->awaitingSeed = [];
        foreach ($seeds as $fn) {
            try {
                // false is a failure, e.g. of ModelBase::bulkInsert() given as fn () => Model::bulkInsert([...])
                if ($fn() === false) {
                    Log::critical('Failed to write the migration data: {error}', ['error' => DB::error()]);
                    return false;
                }
            } catch (\Throwable $e) {
                Log::critical('Failed to write the migration data: {error}', ['error' => $e->getMessage()]);
                return false;
            }
        }

        return true;
    }

    /**
     * Commits creation changes
     *
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

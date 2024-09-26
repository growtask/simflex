<?php

namespace Simflex\Core;

use Simflex\Core\DB\Adapter;
use Simflex\Core\User;

/**
 * Class DB
 * @package Simflex\Core
 * @see Adapter
 */
class DB
{
    /**
     * @var Adapter
     */
    protected static $db;
    protected static $lastQuery = '';

    /**
     * @return Adapter
     */
    protected static function db()
    {
        if (!isset(static::$db)) {
            Profiler::traceStart(__CLASS__, __FUNCTION__);
            static::connect();
            Profiler::traceEnd(__CLASS__, __FUNCTION__);
        }
        return static::$db;
    }

    /**
     * Creates new adapter instance
     *
     * @return Adapter
     */
    private static function create(): Adapter
    {
        $class = Container::getConfig()->db['adapter'];
        return new $class;
    }

    /**
     * @return bool
     * @see Adapter::connect()
     */
    public static function connect(): bool
    {
        static::$db = static::create();
        return static::$db->connect();
    }

    /**
     * @param $params
     * @return bool
     * @see Adapter::bind()
     * @deprecated Use $params in query()/assoc()/result() instead
     */
    public static function bind($params): bool
    {
        if (is_array($params)) {
            static::db()->bind($params);
            return true;
        }

        return false;
    }

    /**
     * @param string $q
     * @param array $params
     * @return mixed
     * @see Adapter::query()
     */
    public static function &query(string $q, array $params = [])
    {
        Profiler::traceStart(__CLASS__, __FUNCTION__);
        $execTime = microtime(1);
        $result = static::db()->query($q, $params);

        static::$lastQuery = $q;

        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    /**
     * @param $result
     * @return mixed
     * @see Adapter::fetch()
     */
    public static function fetch(&$result)
    {
        return static::db()->fetch($result);
    }

    /**
     * @param string $q SQL query to execute
     * @param string $field
     * @param array $params
     * @return mixed
     * @see Adapter::result()
     */
    public static function result(string $q, $field = '', array $params = [])
    {
        return static::db()->result(static::query($q, $params), $field);
    }

    /**
     * @param string $q SQL query to execute
     * @param mixed $field1
     * @param mixed $field2
     * @param array $params
     * @return mixed
     * @see Adapter::assoc()
     */
    public static function assoc(string $q, $field1 = false, $field2 = false, array $params = [])
    {
        return static::db()->assoc(static::query($q, $params), $field1, $field2);
    }

    public static function map(string $q, $field1, $field2, array $params = [], bool $list = false): array
    {
        $out = [];
        foreach (self::assoc($q, false, false, $params) as $row) {
            if ($list) {
                $out[$row[$field1]][] = $row[$field2];
            } else {
                $out[$row[$field1]] = $row[$field2];
            }
        }

        return $out;
    }

    public static function arr(string $q, $field1, array $params = []): array
    {
        $out = [];
        foreach (self::assoc($q, false, false, $params) as $row) {
            $out[] = $row[$field1];
        }

        return $out;
    }

    /**
     * @return string
     * @see Adapter::insertId()
     */
    public static function insertId(): string
    {
        return static::db()->insertId();
    }

    /**
     * Returns time delta
     *
     * @param string $time Time point
     * @param int $length String limit
     * @return false|string
     */
    public static function getTime(string $time, int $length = 4)
    {
        $a = explode(' ', $time);
        $b = explode(' ', microtime());
        return substr($b[0] - $a[0] + $b[1] - $a[1], 0, $length + 2);
    }

    /**
     * @return int
     * @see Adapter::errno()
     */
    public static function errno(): int
    {
        return static::db()->errno();
    }

    /**
     * @return string|null
     * @see Adapter::error()
     */
    public static function error(): ?string
    {
        return static::db()->error();
    }

    /**
     * @return string|null
     * @see Adapter::errorCode()
     */
    public static function errorCode(): ?string
    {
        return static::db()->errorCode();
    }

    /**
     * Escapes array or string
     *
     * @param array|string $mixed Target to escape
     * @return array|string
     * @see Adapter::escape()
     */
    public static function escape($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $str) {
                $mixed[$index] = static::escape($str);
            }

            return $mixed;
        }

        return static::db()->escape($mixed);
    }

    /**
     * Enumerates possible values for column
     *
     * @param string $table Table name
     * @param string $field Column name
     * @return array All possible values
     */
    public static function enumValues(string $table, string $field): array
    {
        $buffer = &$_ENV['enum_values'][$table][$field];

        if (!isset($buffer)) {
            $row = DB::result("SHOW FULL COLUMNS FROM `$table` LIKE '$field'");
            $names = explode(';;', $row['Comment']);

            $enumArray = [];
            preg_match_all("/'(.*?)'/", $row['Type'], $enumArray);

            $enumFields = $enumArray[1];
            if (count($names) == count($enumFields)) {
                $ret = [];
                foreach ($names as $index => $name) {
                    $ret[$enumFields[$index]] = trim($name);
                }

                $buffer = $ret;
            } else {
                $buffer = [];
                foreach ($enumFields as $name) {
                    $buffer[$name] = $name;
                }
            }
        }

        return $buffer;
    }

    /**
     * Returns column information
     *
     * @param string $table Table name
     * @param string $field Column name
     * @return mixed
     */
    public static function columnInfo(string $table, string $field)
    {
        return DB::result("SHOW FULL COLUMNS FROM `$table` LIKE '$field'");
    }

    /**
     * @return int
     * @see Adapter::affectedRows()
     */
    public static function affectedRows(): int
    {
        return static::db()->affectedRows();
    }

    /**
     * Starts DB transaction
     */
    public static function transactionStart()
    {
        static::db()->beginTransaction();
    }

    /**
     * Commits DB transaction
     */
    public static function transactionCommit()
    {
        static::db()->commitTransaction();
    }

    /**
     * Rolls back DB transaction
     */
    public static function transactionRollback()
    {
        static::db()->rollbackTransaction();
    }

    /**
     * Ends transaction
     *
     * @param bool $success True to commit, false to rollback
     */
    public static function transactionEnd(bool $success)
    {
        $success ? static::transactionCommit() : static::transactionRollback();
    }

    /**
     * @param mixed $r Adapter-specific result
     * @param int $index Index
     * @return bool
     * @see Adapter::seek()
     */
    public static function seek(&$r, int $index): bool
    {
        return static::db()->seek($r, $index);
    }

    /**
     * Resets result to position 0
     *
     * @param mixed $r Adapter-specific result
     * @return bool
     */
    public static function fetchReset(&$r): bool
    {
        return static::seek($r, 0);
    }

    public static function wrapString(string $s): string
    {
        return '\'' . self::escape($s) . '\'';
    }

    public static function wrapName(string $s): string
    {
        return '`' . $s . '`';
    }

    public static function getLastQuery(): string
    {
        return static::$lastQuery;
    }
}

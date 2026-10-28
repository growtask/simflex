<?php

namespace Simflex\DB;

use Simflex\Core\Container;
use Simflex\Debug\Profiler;

/**
 * Класс DB
 * @package Simflex\Core
 * @see Adapter
 */
class DB
{
    protected static Adapter $db;
    protected static string $lastQuery = '';

    protected static function db(): Adapter
    {
        if (!isset(static::$db)) {
            Profiler::traceStart(__CLASS__, __FUNCTION__);
            static::connect();
            Profiler::traceEnd(__CLASS__, __FUNCTION__);
        }
        return static::$db;
    }

    /**
     * Создаёт новый экземпляр адаптера
     */
    private static function create(): Adapter
    {
        $class = Container::getConfig()->db['adapter'];
        return new $class();
    }

    /**
     * @see Adapter::connect()
     */
    public static function connect(): bool
    {
        static::$db = static::create();
        return static::$db->connect();
    }

    /**
     * @see Adapter::query()
     */
    public static function query(string $q, array $params = []): mixed
    {
        Profiler::traceStart(__CLASS__, __FUNCTION__);
        $execTime = microtime(1);
        $result = static::db()->query($q, $params);

        static::$lastQuery = $q;

        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    /**
     * @see Adapter::fetch()
     */
    public static function fetch($result): mixed
    {
        return static::db()->fetch($result);
    }

    /**
     * @param string $q SQL-запрос для выполнения
     * @see Adapter::result()
     */
    public static function result(string $q, int|string $field = '', array $params = []): mixed
    {
        return static::db()->result(static::query($q, $params), $field);
    }

    /**
     * @param string $q SQL-запрос для выполнения
     * @see Adapter::assoc()
     */
    public static function assoc(string $q, bool|string $field1 = false, bool|string $field2 = false, array $params = []): bool|array
    {
        return static::db()->assoc(static::query($q, $params), $field1, $field2);
    }

    /**
     * Удаляет строки таблицы по условию, значения условия передаются параметрами запроса. Для таблиц без модели
     * (таблицы связей, кэши); у модели — ModelBase::bulkDelete()
     *
     * @param string|array|Where $where Условие в виде, который принимает Where: ['колонка' => значение или список].
     *        Пустое условие (в том числе пустой список значений) — исключение: оно удалило бы всю таблицу
     */
    public static function delete(string $table, string|array|Where $where): bool
    {
        $where = new Where($where);
        // параметры заполняет приведение условия к строке
        $condition = (string)$where;
        if ($condition === '') {
            throw new \InvalidArgumentException("DB::delete(): empty condition for table $table");
        }

        return (bool)static::query('DELETE FROM ' . static::wrapName($table) . ' ' . $condition, $where->getBinds());
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
     * @see Adapter::insertId()
     */
    public static function insertId(): string
    {
        return static::db()->insertId();
    }

    /**
     * Возвращает разницу во времени
     *
     * @param string $time Момент времени
     * @param int $length Ограничение длины строки
     */
    public static function getTime(string $time, int $length = 4): string
    {
        $a = explode(' ', $time);
        $b = explode(' ', microtime());
        return substr($b[0] - $a[0] + $b[1] - $a[1], 0, $length + 2);
    }

    /**
     * @see Adapter::errno()
     */
    public static function errno(): int
    {
        return static::db()->errno();
    }

    /**
     * @see Adapter::error()
     */
    public static function error(): ?string
    {
        return static::db()->error();
    }

    /**
     * @see Adapter::errorCode()
     */
    public static function errorCode(): ?string
    {
        return static::db()->errorCode();
    }

    /**
     * Экранирует массив или строку
     *
     * @param array|string $mixed Что экранировать
     * @see Adapter::escape()
     */
    public static function escape(array|string $mixed): array|string
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
     * Перечисляет возможные значения столбца
     *
     * @param string $table Имя таблицы
     * @param string $field Имя столбца
     * @return array Все возможные значения
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
            if (count($names) === count($enumFields)) {
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
     * Возвращает сведения о столбце
     *
     * @param string $table Имя таблицы
     * @param string $field Имя столбца
     */
    public static function columnInfo(string $table, string $field): mixed
    {
        return DB::result("SHOW FULL COLUMNS FROM `$table` LIKE '$field'");
    }

    /**
     * @see Adapter::affectedRows()
     */
    public static function affectedRows(): int
    {
        return static::db()->affectedRows();
    }

    /**
     * Выполняет $fn в транзакции: фиксирует её, если $fn завершилась, и откатывает при исключении (исключение
     * пробрасывается дальше).
     *
     * Вложенный вызов выполняется в транзакции внешнего: фиксирует или откатывает всё внешний вызов.
     * DDL-запросы (CREATE, ALTER, DROP) MySQL фиксирует сразу, откатить их нельзя.
     *
     * ```php
     * $orderId = DB::transaction(function () use ($cart) {
     *     $order = Order::insertStatic([...]);
     *     OrderItem::bulkInsert([...]);
     *     return $order->id;
     * });
     * ```
     *
     * @template T
     * @param callable(): T $fn
     * @return T Результат $fn
     */
    public static function transaction(callable $fn): mixed
    {
        $db = static::db();
        if ($db->inTransaction()) {
            return $fn();
        }

        $db->beginTransaction();
        try {
            $result = $fn();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollbackTransaction();
            }
            throw $e;
        }
        if ($db->inTransaction()) {
            $db->commitTransaction();
        }

        return $result;
    }

    /**
     * @param mixed $r Результат в формате адаптера
     * @param int $index Индекс
     * @see Adapter::seek()
     */
    public static function seek(&$r, int $index): bool
    {
        return static::db()->seek($r, $index);
    }

    /**
     * Сбрасывает результат на позицию 0
     *
     * @param mixed $r Результат в формате адаптера
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

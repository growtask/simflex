<?php

namespace Simflex\DB;

use ArrayAccess;
use Exception;

class Where implements ArrayAccess
{
    protected array $data = [];
    protected array $bind = [];

    /**
     * Конструктор Where.
     *
     * @param mixed $where Условия фильтра
     * @throws Exception
     */
    public function __construct(string|array|Where|Expr $where = [])
    {
        if ($where instanceof static) {
            $this->data = $where->data;
            $this->bind = $where->bind;
        }

        if (is_string($where) || $where instanceof Expr) {
            $this->data[] = $where;
        }

        /** @var array $where */
        if (is_array($where)) {
            $this->data = $where;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function getBinds(): array
    {
        return $this->bind;
    }

    /**
     * Преобразует выражение WHERE в строку
     *
     * @param bool $withWhereWord Добавлять ли слово WHERE
     * @return string SQL-выражение WHERE
     * @throws Exception
     */
    public function toString(bool $withWhereWord = true): string
    {
        if (($data = static::prepareData($this->data, $this->bind)) && $data['result']) {
            $this->bind = $data['bind'];
            return ($withWhereWord ? 'WHERE ' : '') . implode(' AND ', $data['result']);
        }

        return '';
    }

    /**
     * @throws Exception
     */
    public function toArray(): array
    {
        return static::prepareData($this->data, $this->bind);
    }

    /**
     * Подготавливает данные WHERE
     *
     * @param array $data Входные данные
     * @param array $oldBinds Прежние параметры запроса (если есть)
     * @return array{result: array, bind: array}
     * @throws Exception
     */
    protected static function prepareData(array $data, array $oldBinds = []): array
    {
        $bind = $oldBinds;
        $result = [];
        foreach ($data as $index => $value) {
            $wrappedIndex = implode('.', array_map(fn($i) => DB::wrapName($i), explode('.', $index)));
            $idCrc = 'id_' . (string)crc32($wrappedIndex);

            if (is_array($value) && !$value) {
                continue;
            } elseif (is_null($value)) {
                $result[] = "$wrappedIndex IS NULL";
            } elseif ((string)$index !== (string)(int)$index) {
                if (is_array($value)) {
                    $values = [];
                    $i = 0;
                    foreach ($value as $val) {
                        $values[] = ':' . $idCrc . '_' . $i;
                        $bind[$idCrc . '_' . $i] = $val;
                        $i++;
                    }

                    $values = implode(',', $values);
                    $result[] = "$wrappedIndex IN ($values)";
                } else {
                    $result[] = "$wrappedIndex = :$idCrc";
                    $bind[$idCrc] = $value;
                }
            } else {
                if (is_array($value)) {
                    throw new Exception('Where: array-valued statement must be associative.');
                } elseif ($value) {
                    $result[] = $value;
                }
            }
        }

        return ['result' => $result, 'bind' => $bind];
    }

    /**
     * Добавляет условия WHERE
     *
     * @param string|array|Where|\Simflex\DB\Expr $where Условия фильтра
     * @throws Exception
     */
    public function add(string|array|Where|Expr $where): void
    {
        $myArr = $this->toArray();

        $other = new static($where);
        $otherArr = $other->toArray();

        $this->data = array_filter(array_merge($myArr['result'], $otherArr['result']));
        $this->bind = array_merge($myArr['bind'], $otherArr['bind']);
    }
}

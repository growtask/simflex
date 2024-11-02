<?php

namespace Simflex\Core\DB;

use ArrayAccess;
use Exception;
use Simflex\Core\DB;

class Where implements ArrayAccess
{
    protected array $data = [];
    protected array $bind = [];

    /**
     * Where constructor.
     *
     * @param mixed $where Filter conditions
     * @throws Exception
     */
    public function __construct(string|array|Where|Expr $where = [])
    {
        if ($where instanceof static) {
            $this->data = $where->data;
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
    public function __toString()
    {
        return $this->toString();
    }

    public function getBinds(): array
    {
        return $this->bind;
    }

    /**
     * Convert WHERE statement to string
     *
     * @param bool $withWhereWord Whether it should include WHERE word
     * @return string SQL WHERE statement
     * @throws Exception
     */
    public function toString(bool $withWhereWord = true): string
    {
        if (($data = static::prepareData($this->data)) && $data[0]) {
            $this->bind = $data[1];
            return ($withWhereWord ? 'WHERE ' : '') . implode(' AND ', $data[0]);
        }

        return '';
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        return static::prepareData($this->data)[0];
    }

    /**
     * Prepare WHERE data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected static function prepareData(array $data): array
    {
        $bind = [];
        $result = [];
        foreach ($data as $index => $value) {
            $wrappedIndex = DB::wrapName($index);

            if (is_array($value) && !$value) {
                continue;
            } elseif (is_null($value)) {
                $result[] = "$wrappedIndex IS NULL";
            } elseif ((string)$index !== (string)(int)$index) {
                if (is_array($value)) {
                    $values = implode(',', array_fill(0, count($value), '?'));
                    $result[] = "$wrappedIndex IN ($values)";
                    $bind = array_merge($bind, $value);
                } else {
                    $result[] = "$wrappedIndex = ?";
                    $bind[] = $value;
                }
            } else {
                if (is_array($value)) {
                    throw new Exception('Where: array-valued statement must be associative.');
                } elseif ($value) {
                    $result[] = $value;
                }
            }
        }

        return [$result, $bind];
    }

    /**
     * Add WHERE conditions
     *
     * @param string|array|Where|\Simflex\Core\DB\Expr $where Filter conditions
     * @return void
     * @throws Exception
     */
    public function add(string|array|Where|Expr $where): void
    {
        $this->data = array_filter(array_merge($this->toArray(), (new static($where))->toArray()));
    }

}
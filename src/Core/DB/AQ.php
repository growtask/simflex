<?php

namespace Simflex\Core\DB;

use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\ModelBase;

/**
 * Class AQ
 * Simplex Framework DataBase Active Query
 */
class AQ
{
    protected string|array $select = '*';
    protected string $from;
    protected string $fromAlias = '';
    /** @var JoinClause[] */
    protected array $join = [];
    protected string|array|Where $where = '';
    protected ?string $orderBy = null;
    protected string $limit = '';
    protected bool $asArray = false;
    /** @var class-string<ModelBase>|null */
    protected ?string $modelClass = null;
    protected string $custom = '';
    protected string $prefix = '';
    protected array $binds = [];

    public function asArray(bool $asArray = true): static
    {
        $this->asArray = $asArray;
        return $this;
    }

    public function select(string|array $fields): static
    {
        $this->select = $fields;
        return $this;
    }

    public function from(string $table, string $alias = ''): static
    {
        $this->from = $table;
        $this->fromAlias = $alias;
        return $this;
    }

    public function fromAlias(string $alias): static
    {
        $this->fromAlias = $alias;
        return $this;
    }

    public function where(string|array|Where $where): static
    {
        $this->where = $where;
        return $this;
    }

    public function andWhere(string|array|Where|Expr $where): static
    {
        $this->where = new Where($this->where);
        $this->where->add($where);
        return $this;
    }

    public function orderBy(?string $orderBy): static
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function limit(int|string $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function custom(string $cst): static
    {
        $this->custom = $cst;
        return $this;
    }

    public function prefix(string $pf): static
    {
        $this->prefix = $pf;
        return $this;
    }

    public function bind(array $arr): static
    {
        $this->binds = array_merge($this->binds, $arr);
        return $this;
    }

    public function setModelClass(string $class): static
    {
        $this->modelClass = Container::getFactory()->getStatic($class);
        return $this;
    }

    protected function getSelect(): string
    {
        if (is_array($this->select)) {
            return implode(', ', $this->select);
        }
        if (is_string($this->select)) {
            return $this->select;
        }
        throw new \Exception('Bad select statement');
    }

    /**
     * @throws \Exception
     */
    public function build(): string
    {
        if ($this->modelClass) {
            foreach ($this->modelClass::aqModifiersDefault() as $modifier) {
                $this->modify($modifier);
            }
        }
        if (empty($this->from) || !is_string($this->from)) {
            throw new \Exception('Bad from statement');
        }
        if ($pf = $this->prefix) {
            $q[] = $pf;
        }
        $q[] = 'SELECT ' . $this->getSelect();
        $q[] = "FROM `$this->from`" . ($this->fromAlias ? (' ' . $this->fromAlias) : '');
        foreach ($this->join as $join) {
            $q[] = $join->toSql($this->from);
        }

        $where = new Where($this->where);
        $q[] = $where->toString();
        $this->bind($where->getBinds());

        if ($orderBy = $this->orderBy) {
            $q[] = 'ORDER BY ' . DB::escape($orderBy);
        }
        if ($limit = (string)$this->limit) {
            $q[] = 'LIMIT ' . DB::escape($limit);
        }
        $q[] = $this->custom;
        return implode(' ', $q);
    }

    /**
     * @throws \Exception
     */
    public function fetchScalar(int|string $column = 0): string|int|float|null
    {
        $q = $this->build();
        $r = DB::query($q, $this->binds);
        $row = DB::fetch($r);
        if (!$row) {
            return null;
        }
        return static::columnValue($row, $column);
    }

    /**
     * Values of one column of all the rows
     *
     * @param int|string $column Column name or index in the selected fields
     * @return list<string|int|float|null>
     * @throws \Exception
     */
    public function fetchColumn(int|string $column = 0): array
    {
        $r = DB::query($this->build(), $this->binds);
        $result = [];
        while ($row = DB::fetch($r)) {
            $result[] = static::columnValue($row, $column);
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    protected static function columnValue(array $row, int|string $column): string|int|float|null
    {
        if (is_int($column)) {
            $row = array_values($row);
        }
        if (!array_key_exists($column, $row)) {
            throw new \Exception("Column $column does not exist in fetched row");
        }
        return $row[$column];
    }

    /**
     * @throws \Exception
     */
    public function fetchOne(): array|ModelBase|null
    {
        $q = $this->build();

        $r = DB::query($q, $this->binds);
        $row = DB::fetch($r);
        if (!$row) {
            return null;
        }
        if ($this->asArray) {
            return $row;
        }
        if (empty($this->modelClass)) {
            throw new \Exception('Model class not specified');
        }
        return (new $this->modelClass())->fill($row);
    }

    /**
     * @return array|ModelBase[]
     * @throws \Exception
     */
    public function all(string|array|bool|null $assocKey = null): array
    {
        if ((is_bool($assocKey) || !$this->asArray) && empty($this->modelClass)) {
            throw new \Exception('Model class not specified');
        }
        $q = $this->build();
        $r = DB::query($q, $this->binds);
        $result = [];
        $counter = 0;
        while ($row = DB::fetch($r)) {
            $firstIteration = $counter == 0;
            if ($this->asArray) {
                $payload = $row;
            } else {
                $payload = (new $this->modelClass())->fill($row);
            }

            if ($assocKey === true) {
                $result[$row[$this->modelClass::getPrimaryKeyName()]] = $payload;
            } elseif (is_array($assocKey)) {
                $assocKey = array_values($assocKey);
                switch (count($assocKey)) {
                    case 1:
                        $result[$row[$assocKey[0]]] = $payload;
                        break;
                    case 2:
                        $result[$row[$assocKey[0]]][$row[$assocKey[1]]] = $payload;
                        break;
                    case 3:
                        $result[$row[$assocKey[0]]][$row[$assocKey[1]]][$row[$assocKey[2]]] = $payload;
                        break;
                    case 0:
                    default:
                        $result[] = $payload;
                }
            } elseif (is_string($assocKey)) {
                $result[$row[$assocKey]] = $payload;
            } else {
                $result[] = $payload;
            }
            $counter++;
        }
        return $result;
    }

    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * @throws \Exception
     * @see Core/DB/HowTo/UsingModifiers.md
     */
    public function modify(string $modifier, ...$params): static
    {
        if (empty($this->modelClass)) {
            throw new \Exception("Model class must be specified for modifier $modifier");
        }
        $modifierAction = 'aqModify' . ucfirst($modifier);
        if (!method_exists($this->modelClass, $modifierAction)) {
            throw new \Exception("There is no modifier $modifier in $this->modelClass");
        }
        $this->modelClass::$modifierAction($this, ...$params);
        return $this;
    }

    /**
     * @param string $type for example INNER, LEFT, RIGHT, FULL
     * @throws \Exception
     * @see Core/DB/HowTo/UsingJoin.md
     */
    public function join(
        string|ModelBase $table,
        string $joinColumn1,
        ?string $joinColumn2 = null,
        string $type = 'INNER',
        string|array|Where|Expr|null $extraOnConditions = null
    ): static {
        $this->join[] = new JoinClause($table, $joinColumn1, $joinColumn2, $type, $extraOnConditions);
        return $this;
    }

    /**
     * @throws \Exception
     * @see Core/DB/HowTo/UsingJoin.md
     */
    public function leftJoin(
        string|ModelBase $table,
        string $joinColumn1,
        ?string $joinColumn2 = null,
        string|array|Where|Expr|null $extraOnConditions = null
    ): static {
        $this->join[] = new JoinClause($table, $joinColumn1, $joinColumn2, 'LEFT', $extraOnConditions);
        return $this;
    }
}

<?php

namespace Simflex\DB\Schema;

use Simflex\DB\DB;
use Simflex\DB\Schema\Params\TableParams;

class Table extends ElementBase
{
    protected TableParams $params;

    protected TableParams $oldParams;

    /** @var \Simflex\DB\Schema\Element[] */
    protected array $elements = [];

    /** @var \Simflex\DB\Schema\Element[] */
    protected array $oldElements = [];

    protected bool $isExisting;
    protected array $dropQueue = [];

    public function __construct(string $name, bool $isExisting = false)
    {
        $this->params = new TableParams();
        $this->name = $name;
        $this->isExisting = $isExisting;
    }

    public function keepParams(): void
    {
        if (!$this->isExisting) {
            return;
        }

        $this->oldParams = $this->params;
        $this->oldElements = $this->elements;
    }

    // ------------ ПОСТРОЕНИЕ ------------ //

    /**
     * Создавать таблицу, только если её ещё нет
     */
    public function ifNotExists(): static
    {
        $this->params->ifNotExists = true;
        return $this;
    }

    /**
     * Создавать временную таблицу
     */
    public function temporary(): static
    {
        $this->params->temporary = true;
        return $this;
    }

    /**
     * Задаёт счётчик автоинкремента
     */
    public function autoIncrement(int $n): static
    {
        $this->params->autoIncrement = $n;
        return $this;
    }

    /**
     * Задаёт комментарий таблицы
     */
    public function comment(string $comment): static
    {
        $this->params->comment = $comment;
        return $this;
    }

    /**
     * Задаёт кодировку таблицы
     *
     * @param string $charset Кодировка
     * @param bool $isDefault Модификатор DEFAULT
     */
    public function characterSet(string $charset, bool $isDefault = false): static
    {
        $this->params->characterSet = $charset;
        $this->params->defaultCharacterSet = $isDefault;

        return $this;
    }

    /**
     * Добавляет столбец
     *
     * @param string $name Имя столбца
     * @param string $type Тип столбца
     * @param int|null $length Длина типа столбца
     */
    public function addColumn(string $name, string $type, ?int $length = null): Column
    {
        $col = new Column($name, $this);
        $col->dataType($type, $length);

        $this->elements[$name] = $col;
        return $col;
    }

    /**
     * Добавляет ограничение
     *
     * @param string $name Имя ограничения
     */
    public function addConstraint(string $name = ''): Constraint
    {
        $const = new Constraint($name);

        $this->elements[$name ?: md5(microtime())] = $const;
        return $const;
    }

    /**
     * Добавляет индекс
     *
     * @param string $name Имя индекса
     */
    public function addIndex(string $name = ''): Index
    {
        $idx = new Index($name);

        $this->elements[$name ?: md5(microtime())] = $idx;
        return $idx;
    }

    /**
     * Удаляет столбец
     *
     * @param string|array $name Имя (имена) столбца
     */
    public function dropColumns(string|array $name): static
    {
        if (is_array($name)) {
            $this->dropQueue = array_unique(array_merge($this->dropQueue, $name));
        } else {
            $this->dropQueue[] = $name;
        }

        return $this;
    }

    // ------------ ХЕЛПЕРЫ ------------ //

    public function id(string $name): Column
    {
        return $this->integer($name)->primaryKey()->autoIncrement();
    }

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

    public function longText(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_LONGTEXT);
    }

    public function boolean(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_TINYINT, 1);
    }

    public function price(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_DECIMAL . '(10,2)');
    }

    public function date(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_DATE);
    }

    public function dateTime(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_DATE_TIME);
    }

    public function timestamp(string $name): Column
    {
        return $this->addColumn($name, Column::TYPE_TIMESTAMP);
    }

    public function enum(string $name, array $items): Column
    {
        return $this->addColumn(
            $name,
            Column::TYPE_ENUM . '(' . implode(
                ',',
                array_map(function ($i) {
                    return "'$i'";
                }, $items)
            ) . ')'
        );
    }

    // ------------ УТИЛИТЫ ------------ //

    /**
     * Возвращает параметры
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

        // удаляем столбцы
        foreach ($this->dropQueue as $col) {
            if (!$firstCmd) {
                $sql .= ', ';
            }

            $sql .= 'DROP COLUMN ' . DB::wrapName($col);
            $firstCmd = false;
        }

        /** @var \Simflex\DB\Schema\Element[] $toMod */
        $toMod = [];

        /** @var \Simflex\DB\Schema\Element[] $toMod */
        $toAdd = [];

        foreach ($this->elements as $p) {
            if (!($p instanceof Column)) {
                continue;
            }

            $found = false;
            foreach ($this->oldElements as $op) {
                if (!($op instanceof Column)) {
                    continue;
                }

                if ($p->getName() === $op->getName()) {
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

        // изменяем существующие
        foreach ($toMod as $col) {
            if (!$firstCmd) {
                $sql .= ', ';
            }

            $sql .= 'MODIFY COLUMN ' . $col->toString();
            $firstCmd = false;
        }

        // добавляем новые
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

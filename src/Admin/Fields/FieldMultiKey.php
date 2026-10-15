<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Base;
use Simflex\Core\DB;

class FieldMultiKey extends Field
{
    private static array $buffer;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value): string
    {
        $fields = ['*'];

        $value = (int)@$_GET[$this->tablePk];
        if ($this->tablePk == 'product_id' && $this->params['key'] == 'product_id') {
            $this->tablePk = 'link_id';
        }

        if ($value) {
            $fields[] = "(select count(*) from {$this->params['table_relations']} where $this->tablePk = $value and {$this->params['key']} = t.{$this->params['key']}) checked";
        }

        $q = 'SELECT ' . implode(', ', $fields) . " FROM {$this->params['table_values']} t order by {$this->params['key']}";
        $rows = DB::assoc($q);

        $valReal = [];
        foreach ($rows as &$row) {
            // remove this hack as well
            if ($this->table == 'catalog_product' && $row['pid']) {
                $par = DB::result('select name from catalog_category where category_id = ?', 0, [$row['pid']]);
                if ($par) {
                    $row['name'] .= ' (' . $par . ')';
                }
            }

            if ($row['checked']) {
                $valReal[] = $row[(string)$this->params['key']];
            }
        }
        unset($row);

        $items = [];
        foreach ($rows as $row) {
            $items[] = [$row[(string)$this->params['key']], $row[(string)$this->params['key_alias']]];
        }

        return $this->render('tags', [
            'readonly' => true,
            'ajax' => false,
            'addNew' => false,
            'name' => $this->name,
            'value' => implode(',', $valReal),
            'items' => $items,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $pkValue = (int)@$_REQUEST[$this->tablePk];
        if ($this->tablePk == 'product_id' && $this->params['key'] == 'product_id') {
            $this->tablePk = 'link_id';
        }

        if (!$pkValue) {
            return '';
        }

        $values = isset($_POST[$this->name]) ? explode(',', $_POST[$this->name]) : [];
        $q = "DELETE FROM {$this->params['table_relations']} where $this->tablePk = $pkValue";
        DB::query($q);

        foreach ($values as $value) {
            $value = (int)$value;
            if (!$value) {
                continue;
            }

            $q = "INSERT INTO {$this->params['table_relations']} set $this->tablePk = $pkValue, {$this->params['key']} = $value";
            DB::query($q);
        }

        return '';
    }

    public function show($row): string
    {
        $pkValue = (int)$row[$this->tablePk];
        if (!isset(self::$buffer)) {
            $q = "SELECT {$this->params['key']}, {$this->params['key_alias']} FROM {$this->params['table_values']}";
            $values = DB::assoc($q, $this->params['key']);
            $where = Base::$currentWhere;
            $q = "
                SELECT $this->tablePk,
                    (SELECT {$this->params['key_alias']} FROM {$this->params['table_values']} WHERE {$this->params['key']} = r.{$this->params['key']}) label
                FROM {$this->params['table_relations']} r WHERE $this->tablePk IN (
                    SELECT $this->tablePk FROM $this->table $where
                )
            ";
            $rels = DB::assoc($q, $this->tablePk, 'label');
            foreach ($rels as $key => $keyAliases) {
                self::$buffer[$key] = implode(', ', array_keys($keyAliases));
            }
        }

        return $this->cell((string)@self::$buffer[$pkValue]);
    }

    public function filter($value): string
    {
        $value = DB::escape($value);

        if ($this->table == 'catalog_product') {
            // a dumb hack again...
            if (!$value) {
                return '';
            }

            $q = DB::query(
                "select {$this->params['key_alias']}, pid from {$this->params['table_values']} where {$this->params['key']} = $value"
            );
        } else {
            $q = DB::query(
                "select {$this->params['key_alias']} from {$this->params['table_values']} where {$this->params['key']} = $value"
            );
        }
        $r = DB::fetch($q);

        // one more hack
        if ($this->table == 'catalog_product') {
            if ($r['pid']) {
                $par = DB::result('select name from catalog_category where category_id = ?', 0, [$r['pid']]);
                if ($par) {
                    $r['name'] .= ' (' . $par . ')';
                }
            }
        }

        return $this->dropdown([
            'name' => 'filter[' . $this->name . ']',
            'value' => $value,
            'action' => 'searchMultiKey',
            'search' => true,
            'items' => [[$value, $r[$this->params['key_alias']]]],
        ]);

    }

    public function selectQueryField(): string
    {
        return '';
    }

    public static function typeLabel(): string
    {
        return 'Связь многие ко многим';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'table_values',
                'label' => 'Таблица сущностей',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'table_relations',
                'label' => 'Таблица связей',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'key',
                'label' => 'Ключ таблицы сущностей',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'key_alias',
                'label' => 'Поле-ярлык у сущности',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => 'Например name у catalog_category',
                'default_value' => '',
            ],
        ];
    }
}

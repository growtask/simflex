<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\AdminBase;
use Simflex\Core\DB;
use Simflex\Admin\Fields\Field;

class FieldMultiKey extends Field
{

    private static $buffer;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value)
    {
        $fields = array("*");
        $value = (int)@$_GET[$this->tablePk];
        if ($value) {
            $fields[] = "(select count(*) from {$this->params['table_relations']} where $this->tablePk = $value and {$this->params['key']} = t.{$this->params['key']}) checked";
        }

        // bro!
        if ($this->table == 'catalog_product') {
            $fields[] = 'pid';
        }

        $q = "SELECT " . implode(', ', $fields) . " FROM {$this->params['table_values']} t";
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

        $valReal = implode(',', $valReal);

        echo '<div class="form-control__tags" data-readonly="true">
                                                <input class="form-control__tags-value" value="'.$valReal.'" type="hidden" name="'.$this->name.'">
                                                <ul class="form-control__tags-list">
                                                    <input type="text" class="form-control__tags-input">
                                                </ul>
                                                <div class="form-control__tags-popup list1">
                                                    <div class="list1__wrapper">
                                                        <ul class="list1__items">
';

for ($i = 0; $i < count($rows); ++$i) {
            echo '<li data-value="'.$rows[$i][(string)$this->params['key']].'" class="list1__item">'.$rows[$i][(string)$this->params['key_alias']].'</li>';
}

        echo ' </ul>
                                                    </div>
                                                </div>
                                            </div>';

//
//
//        echo '<div class="checkbox-list">';
//        foreach ($rows as $row) {
//            echo '<label class="checkbox-inline"><input type="checkbox" name="' . $this->name . '[' . $row[(string)$this->params['key']] . ']" value="' . $row[(string)$this->params['key']] . '"' . (!empty($row['checked']) ? ' checked' : '') . ' /> ' . $row[(string)$this->params['key_alias']] . '</label>';
//        }
//        echo '</div>';
    }

    public function getPOST($simple = false, $group = null)
    {
        $pkValue = (int)@$_REQUEST[$this->tablePk];
        $values = isset($_POST[$this->name]) ? explode(',', $_POST[$this->name]) : array();
        $q = "DELETE FROM {$this->params['table_relations']} where $this->tablePk = $pkValue";
        DB::query($q);
        foreach ($values as $value) {
            $value = (int)$value;
            $q = "INSERT INTO {$this->params['table_relations']} set $this->tablePk = $pkValue, {$this->params['key']} = $value";
            DB::query($q);
        }
        return '';
    }

    public function show($row)
    {
        $pkValue = (int)$row[$this->tablePk];
        if (!isset(self::$buffer)) {
            $q = "SELECT {$this->params['key']}, {$this->params['key_alias']} FROM {$this->params['table_values']}";
            $values = DB::assoc($q, $this->params['key']);
            $where = '';
            if (class_exists('AdminBase')) {
                $where = AdminBase::$currentWhere;
            }
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

        echo '<div class="table__row-' . $this->name . ' ' . ($this->fk ? 'table__row-id' : '') . ' table__row-text">' . (string)@self::$buffer[$pkValue] . '</div>';
    }

    public function filter($value)
    {
        $value = DB::escape($value);

        if ($this->table == 'catalog_product') {
            // a dumb hack again...
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

        echo <<<HTML
<div class="form-control form-control--sm">
    <div class="form-control__dropdown" data-action="searchMultiKey" data-ajax="true">
        <div class="form-control__dropdown-top">
            <input class="form-control__dropdown-input" value="$value" type="hidden" name="filter[{$this->name}]" >
            <input placeholder="Начните вводить название..." class="form-control__dropdown-text" type="text">
            <div class="form-control__dropdown-current">—</div>
            <button class="form-control__dropdown-toggle" type="button">
                <svg viewBox="0 0 24 24">
                    <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                </svg>
            </button>
        </div>
        <div class="form-control__dropdown-list">
            <div data-value="{$value}" class="form-control__dropdown-item">{$r[$this->params['key_alias']]}</div>
        </div>
    </div>
</div>
HTML;

    }

    public function selectQueryField()
    {
        return '';
    }

}

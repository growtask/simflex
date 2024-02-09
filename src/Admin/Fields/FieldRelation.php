<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldRelation extends Field
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value)
    {
        $rows = [];

        $id = (int)$_REQUEST[$this->tablePk];

        $q = "select `{$this->tablePk}`, `{$this->params['name']}` from {$this->table} t where 
        (select count(*) from {$this->params['relation']} p where 
                                          (p.{$this->params['right']} = {$id} and p.{$this->params['left']} = t.{$this->tablePk}) or 
                                          (p.{$this->params['left']} = {$id} and p.{$this->params['right']} = t.{$this->tablePk})) > 0";

        $q = DB::query($q);

        while ($r = DB::fetch($q)) {
            $rows[] = $r;
        }

        $value = [];
        foreach ($rows as $row) {
            $value[] = $row[$this->tablePk];
        }

        $value = implode(',', $value);

        echo '<div class="form-control__tags" data-readonly="true" data-ajax="true">
<input class="form-control__tags-value" value="' . $value . '" type="hidden" name="' . $this->name . '">
<ul class="form-control__tags-list">
    <input type="text" class="form-control__tags-input">
</ul>
<div class="form-control__tags-popup list1">
    <div class="list1__wrapper">
        <ul class="list1__items">
';

        foreach ($rows as $row) {
            echo '<li data-value="' . $row[$this->tablePk] . '" class="list1__item">' . $row[$this->params['name']] . '</li>';
        }

        echo ' </ul>
        </div>
    </div>
</div>';
    }

    public function getPOST($simple = false, $group = null)
    {
        $in = explode(',', $_POST[$this->name]);
        $id = (int)$_REQUEST[$this->tablePk];

        $existing = [];
        $q = "select `{$this->tablePk}`, `{$this->params['name']}` from {$this->table} t where 
        (select count(*) from {$this->params['relation']} p where 
                                          (p.{$this->params['right']} = {$id} and p.{$this->params['left']} = t.{$this->tablePk}) or 
                                          (p.{$this->params['left']} = {$id} and p.{$this->params['right']} = t.{$this->tablePk})) > 0";

        $q = DB::query($q);

        while ($r = DB::fetch($q)) {
            $existing[] = $r[$this->tablePk];
        }

        $toDel = [];
        $toAdd = [];
        foreach ($in as $i) {
            $toAdd[] = $i;
        }

        foreach ($existing as $e) {
            $toDel[] = $e;
        }

        if ($toDel) {
            $toDelQ = [];
            foreach ($toDel as $a) {
                $toDelQ[] = [$id, $a];
            }

            for ($i = 0; $i < count($toDel) - 1; ++$i) {
                for ($k = $i + 1; $k < count($toDel); ++$k) {
                    $toDelQ[] = [$toDel[$i], $toDel[$k]];
                }
            }

            foreach ($toDelQ as $q) {
                DB::query("delete from {$this->params['relation']} where {$this->params['left']} = ? and {$this->params['right']} = ?", $q);
            }
        }

        if ($toAdd) {
            $toAddQ = [];
            foreach ($toAdd as $a) {
                $toAddQ[] = "({$id}, {$a})";
            }

            for ($i = 0; $i < count($toAdd) - 1; ++$i) {
                for ($k = $i + 1; $k < count($toAdd); ++$k) {
                    $toAddQ[] = "({$toAdd[$i]}, {$toAdd[$k]})";
                }
            }

            $toAdd = implode(',', $toAddQ);
            DB::query(
                "insert into {$this->params['relation']} ({$this->params['left']}, {$this->params['right']}) values {$toAdd}"
            );
        }

        return '';
    }
}
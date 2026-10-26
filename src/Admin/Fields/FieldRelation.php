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

    public function input($value): string
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

        $items = [];
        foreach ($rows as $row) {
            $items[] = [$row[$this->tablePk], $row[$this->params['name']]];
        }

        return $this->render('tags', [
            'readonly' => true,
            'ajax' => true,
            'addNew' => false,
            'name' => $this->name,
            'value' => $value,
            'items' => $items,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
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
                DB::query(
                    "delete from {$this->params['relation']} where {$this->params['left']} = ? and {$this->params['right']} = ?",
                    $q
                );
            }
        }

        if ($toAdd) {
            $toAddQ = [];
            foreach ($toAdd as $a) {
                if ($id && $a) {
                    $toAddQ[] = "({$id}, {$a})";
                }
            }

            for ($i = 0; $i < count($toAdd) - 1; ++$i) {
                for ($k = $i + 1; $k < count($toAdd); ++$k) {
                    if ($toAdd[$i] && $toAdd[$k]) {
                        $toAddQ[] = "({$toAdd[$i]}, {$toAdd[$k]})";
                    }
                }
            }

            if ($toAddQ) {
                $toAdd = implode(',', $toAddQ);
                DB::query(
                    "insert into {$this->params['relation']} ({$this->params['left']}, {$this->params['right']}) values {$toAdd}"
                );
            }
        }

        return '';
    }
}

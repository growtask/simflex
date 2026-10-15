<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldNPP extends Field
{
    public function show($row): string
    {
        return $this->render('npp', [
            'name' => $this->name,
            'tablePk' => $this->tablePk,
            'value' => $row[$this->name],
            'pkValue' => $row[$this->tablePk],
        ]);
    }

    public function input($value): string
    {
        if (!isset($_REQUEST[$this->tablePk])) {
            // TODO: make it nice
            if ($this->table == 'catalog_product') {
                $value = 1;
            } else {
                $where = [];
                if ($filter = @$_SESSION[$this->table]['filter']) {
                    foreach ($filter as $fname => $fval) {
                        if ($fval) {
                            $where[] = "$fname = '$fval'";
                        }
                    }
                }
                $q = "SELECT MAX($this->name) FROM $this->table" . (count($where) ? ' WHERE ' . implode(
                    ' AND ',
                    $where
                ) : '');
                $max = DB::result($q, 0);
                $value = $max + 1;
            }
        }

        return parent::input($value);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        return $this->e2n && $_POST[$this->name] === '' ? null : (int)$_POST[$this->name];
    }

    public static function typeLabel(): string
    {
        return '№ п/п';
    }

    public static function typeParams(): array
    {
        return [];
    }
}

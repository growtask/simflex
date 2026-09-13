<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldEnum extends FieldString
{
    protected string $changeURL = '';

    public function input($value): string
    {
        return $this->dropdown([
            'name' => $this->name,
            'value' => !$value ? '' : $value,
            'onchange' => $this->onchange,
            'items' => $this->dropdownItems($this->e2n),
        ]);
    }

    protected function dropdownItems(bool $withEmpty): array
    {
        $items = $withEmpty ? [['', '—']] : [];
        foreach ($this->fetchValues() as $k => $v) {
            $items[] = [$k, $v];
        }
        return $items;
    }

    protected function fetchValues(): array
    {
        $buffer = &$_ENV['enum_values'][$this->table][$this->name];

        if (!isset($buffer)) {
            $q = "SHOW FULL COLUMNS FROM `$this->table` LIKE '$this->name'";
            $row = DB::result($q);
            $enumArray = [];
            preg_match_all("/'(.*?)'/", $row['Type'], $enumArray);
            $enumFields = $enumArray[1];
            $names = explode(';;', $row['Comment']);
            if (count($names) == count($enumFields)) {
                $ret = [];
                foreach ($names as $index => $name) {
                    $ret[$enumFields[$index]] = trim($name);
                }
                $buffer = $ret;
            } else {
                if ($this->params['enum'] ?? '') {
                    $buffer = [];
                    foreach (explode(';;', $this->params['enum']) as $kv) {
                        $kvv = explode('=', $kv);
                        $buffer[$kvv[0]] = $kvv[1];
                    }
                } else {
                    $buffer = [];
                    foreach ($enumFields as $name) {
                        $buffer[$name] = $name;
                    }
                }
            }
        }

        return $buffer;
    }

    public function filter($value): string
    {
        if ($this->filter) {
            return $this->dropdown([
                'name' => 'filter[' . $this->name . ']',
                'value' => $value,
                'items' => $this->dropdownItems(true),
            ]);
        }
        return '';
    }

    public function showDetail($row): ?string
    {
        $value = $row[$this->name];
        $values = $this->fetchValues();
        return @$values[$value];
    }

    public function show($row): string
    {
        $values = $this->fetchValues();

        return $this->dropdown([
            'name' => $this->name,
            'value' => @$values[$row[$this->name]],
            'data' => ['data-enum' => $this->tablePk, 'data-enumv' => $row[$this->tablePk]],
            'items' => $this->dropdownItems(true),
        ]);
    }

    public static function typeLabel(): string
    {
        return 'Варианты';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'enum',
                'label' => 'Варианты (key=value;;)',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
        ];
    }
}

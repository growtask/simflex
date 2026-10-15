<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;
use Simflex\Admin\Fields\FieldString;

class FieldEnum extends FieldString
{
    protected string $changeURL = '';

    public function input($value): string
    {

        $select = '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown">
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" onchange="'.$this->onchange.'" value="'.(!$value ? '' : $value).'" type="hidden" name="' . $this->name . '" >
                                <div class="form-control__dropdown-current">—</div>
                                <button type="button" class="form-control__dropdown-toggle">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                           
                                        ';
        if ($this->e2n) {
            $select .= '<div data-value="" class="form-control__dropdown-item">—</div>';
        }

        foreach ($this->fetchValues($this->table, $this->name) as $k => $v) {
            $select .= '<div data-value="'.$k.'" class="form-control__dropdown-item">'.$v.'</div>';
        }

        $select .= '</div>
                                    </div>
                                </div>';

        return $select;
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

    public function filter($value): void
    {
        if ($this->filter) {
            $select = '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown">
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" value="'.$value.'" type="hidden" name="filter[' . $this->name . ']" >
                                <div class="form-control__dropdown-current">—</div>
                                <button class="form-control__dropdown-toggle" type="button">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                
                            ';

            $select .= '<div data-value="" class="form-control__dropdown-item">—</div>';
            foreach ($this->fetchValues($this->table, $this->name) as $k => $v) {
                $select .= '<div data-value="'.$k.'" class="form-control__dropdown-item">'.$v.'</div>';
            }

            $select .= '</div>
                                    </div>
                                </div>';

            echo $select;
        }
    }

    public function showDetail($row): ?string
    {
        $value = $row[$this->name];
        $values = $this->fetchValues();
        return @$values[$value];
    }

    public function show($row): void
    {
        $value0 = $row[$this->name];
        $pkValue = $row[$this->tablePk];
        $values = $this->fetchValues();

        $value = @$values[$value0];

        $select = '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown">
                            <div class="form-control__dropdown-top">
                                <input data-enum="'.$this->tablePk.'" data-enumv="'.$pkValue.'" class="form-control__dropdown-input" value="'.$value.'" type="hidden" name="'.$this->name.'" >
                                <div class="form-control__dropdown-current">—</div>
                                <button class="form-control__dropdown-toggle" type="button">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                
                            ';

        $select .= '<div data-value="" class="form-control__dropdown-item">—</div>';
        foreach ($this->fetchValues($this->table, $this->name) as $k => $v) {
            $select .= '<div data-value="'.$k.'" class="form-control__dropdown-item">'.$v.'</div>';
        }

        $select .= '</div>
                                    </div>
                                </div>';

        echo $select;
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

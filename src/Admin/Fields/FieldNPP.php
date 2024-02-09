<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\AdminBase;
use Simflex\Core\DB;
use Simflex\Admin\Fields\Field;

class FieldNPP extends Field
{

    public function show($row)
    {
        $value = $row[$this->name];
        $pkValue = $row[$this->tablePk];

        echo <<<DATA
<div class="table__row-quantity form-control">
                            <div class="form-control__quantity" data-npp="$pkValue" data-content="$this->tablePk">
                                <a href="?action=change_npp&field=$this->name&$this->tablePk=$pkValue&up" class="form-control__quantity-minus">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect y="6" width="2" height="10" rx="1" transform="rotate(-90 0 6)"
                                            fill="white" />
                                    </svg>
                                </a>
                                <input type="text" value="$value" class="form-control__quantity-input" />
                                <a href="?action=change_npp&field=$this->name&$this->tablePk=$pkValue&down" class="form-control__quantity-plus">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect x="4" width="2" height="10" rx="1" fill="white" />
                                        <rect y="6" width="2" height="10" rx="1" transform="rotate(-90 0 6)"
                                            fill="white" />
                                    </svg>
                                </a>
                            </div>
                        </div>
DATA;
    }

    public function input($value)
    {
        if (!isset($_REQUEST[$this->tablePk])) {
            // TODO: make it nice
            if ($this->table == 'catalog_product') {
                $value = 1;
            } else {
                $where = array();
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
    
    public function getPOST($simple = false, $group = null)
    {
        return $this->e2n && $_POST[$this->name] === '' ? 'NULL' : (int)$_POST[$this->name];
    }

}
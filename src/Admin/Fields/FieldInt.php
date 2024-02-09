<?php

namespace Simflex\Admin\Fields;


use Simflex\Core\DB;
use Simflex\Core\DB\AQ;
use Simflex\Core\Service;
use Simflex\Admin\Fields\Field;

class FieldInt extends Field
{

    public $defaultValue = 0;

    public $filterAndWhere = '';
    /** @var callable return [x => [y => [label => z], q => [label => k]]] */
    public $filterDataProvider;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isNumeric = !$this->fk;
        $this->filterDataProvider = function () {
            return $this->tree();
        };
    }

    protected function bigint($value)
    {
        return sprintf("%.0f", $value);
    }

    public function &tree()
    {
        $tree = array();
        $q = "
            SELECT *,
                (SELECT class FROM struct_field WHERE field_id = s.field_id) class
            FROM struct_data s
            WHERE table_id = (SELECT table_id FROM struct_table WHERE name = '{$this->fk->table}')
        ";
        $fkTableFields = DB::assoc($q);
        $fkPID = 0;
        foreach ($fkTableFields as $fkTableField) {
            if ($fkTableField['class'] == 'FieldInt') {
                $fkParams = unserialize($fkTableField['params']);
                if (!empty($fkParams['main']['fk_is_pid'])) {
                    $fkPID = $fkTableField['name'];
                    break;
                }
            }
        }

        $query = (new AQ)
            ->select("{$this->fk->key} id, $fkPID pid, {$this->fk->label} label")
            ->from($this->fk->table)
            ->orderBy($this->fk->key);
        if (isset($_REQUEST[$this->fk->key])) {
            $query->andWhere("{$this->fk->key} <> " . $this->bigint($_REQUEST[$this->fk->key]));
        }
        if ($this->filterAndWhere) {
            $query->andWhere($this->filterAndWhere);
        }
        $r = DB::query($query);
        while ($row = DB::fetch($r)) {
            $tree[$this->bigint($row['pid'])][$this->bigint($row['id'])] = $row;
        }
        return $tree;
    }

    public function input($value)
    {
        if ($this->fk) {
//            $tree = is_callable($this->filterDataProvider) ? call_user_func($this->filterDataProvider) : [];
//            $list = Service::tree2list($tree);

            $sel = DB::query("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name from 
                                      `{$this->params['fk_table']}` where {$this->params['fk_key']} = ?", [$value]);
            $sel = DB::fetch($sel);

            $select = '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown" data-action="searchInt" data-ajax="true">
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" onchange="'.$this->onchange.'" value="'.(!$value ? '' : $value).'" type="hidden" name="' . $this->name . '" >
                               <input placeholder="Начните вводить название..." class="form-control__dropdown-text" type="text">
                                <div class="form-control__dropdown-current">'.$sel['name'].'</div>
                                <button type="button" class="form-control__dropdown-toggle">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                           
                                        ';

            $select .= '<div data-value="'.$sel['id'].'" class="form-control__dropdown-item">'.$sel['name'].'</div>';

//            foreach ($list as $id=>$row) {
//                $select .= '<div data-value="'.$id.'" class="form-control__dropdown-item">'. str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $row['tree_level']) . $row['label'] .'</div>';
//            }

            $select .= '</div>
                                    </div>
                                </div>';

            return $select;
        }
        return '<div class="form-control form-control--sm">
                                    <input name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '"' . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"') . ($this->readonly ? ' readonly' : '') . '
                                        type="text" class="form-control__input">
                                </div>';
        }

    public function getPOST($simple = false, $group = null)
    {
        return $this->e2n && $_POST[$this->name] === '' ? NULL : (int)$_POST[$this->name];
    }

    public function filter($value)
    {
        if (!$this->filter) {
            return '';
        }
        if ($this->fk) {
//            $tree = is_callable($this->filterDataProvider) ? call_user_func($this->filterDataProvider) : [];
//            $list = Service::tree2list($tree);

            $sel = DB::query("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name from 
                                      `{$this->params['fk_table']}` where {$this->params['fk_key']} = ?", [$value]);
            $sel = DB::fetch($sel);

            $select =  '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown" data-action="searchInt" data-ajax="true">
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" value="'.$value.'" type="hidden" name="filter[' . $this->name . ']" >
                                <input placeholder="Начните вводить название..." class="form-control__dropdown-text" type="text">
                                <div class="form-control__dropdown-current">—</div>
                                <button class="form-control__dropdown-toggle" type="button">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                
                            ';

//            $select .= '<div data-value="" class="form-control__dropdown-item">—</div>';
//            if ($this->isnull) {
//                $select .= '<div data-value="null" class="form-control__dropdown-item">NULL</div>';
//            }
            $select .= '<div data-value="'.$sel['id'].'" class="form-control__dropdown-item">'.$sel['name'].'</div>';


            $select .= '</div>
                        </div>
                    </div>';

            echo $select;
        } else {
            return parent::filter($value);
        }

    }

}

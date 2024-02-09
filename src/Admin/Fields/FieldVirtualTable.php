<?php
namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldVirtualTable extends Field
{
    public $query = '';
    public $queryCount = '';
    public $cols = [];
    public $button = '';
    public $customjs = '';

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value)
    {
        if (!($_REQUEST[$this->tablePk] ?? 0)) {
            return '<p><em>Для редактирования этой таблицы сначала создайте запись</em></p>';
        }
        
        $value = $this->getValue();
        $jsId = (string)crc32($this->inputName());
        $total = $this->getCount();

        ob_start();
        include 'tpl/vtable.tpl';
        echo $this->customjs;
        return ob_get_clean();
    }

    public function getCount()
    {
        return DB::result($this->queryCount, 0);
    }

    public function getValue()
    {
        $vv = [];
        $q = DB::query($this->query);
        while ($r = DB::fetch($q)) {
            foreach ($r as $k=>$v) {
                if ($k == 'img_json') {
                    $js = json_decode($v, true)['v'] ?? ['v' => ['npp' => 0, 'img' => asset('img/default-img.png')]];
                    usort($js, function ($a, $b) {
                        return $a['npp'] > $b['npp'];
                    });
                    $r['img'] = $js[0]['img'];
                    unset($r['img_json']);
                    continue;
                }

                if (str_starts_with($v, 'RM:')) {
                    $d = explode(';;', substr($v, 3));
                    $r[$k] = $d[0];
                    $r['__rm_id'] = $d[1];
                }
            }

            $vv[] = $r;
        }

        return json_encode([
            's' => $this->cols,
            'v' => $vv,
        ]);
    }

    public function getPOST($simple = false, $group = null)
    {
        return '';
    }
}
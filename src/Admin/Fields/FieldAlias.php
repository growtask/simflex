<?php

namespace Simflex\Admin\Fields;


use Simflex\Core\DB;
use Simflex\Core\Service;
use Simflex\Admin\Fields\Field;

class FieldAlias extends Field
{

    public function getPOST($simple = false, $group = null)
    {
        $val = $_POST[$this->name];
        if (empty($val) && !empty($this->params['source'])) {
            $val = isset($_POST[$this->params['source']]) ? Service::translite($_POST[$this->params['source']]) : '';
        }
        $ret = $this->e2n && $val === '' ? NULL : "" . DB::escape($val) . "";
        return $ret;
    }

    public function defval()
    {
        return '';
    }

}
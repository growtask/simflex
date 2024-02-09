<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;
use Simflex\Core\Helpers\Str;

class FieldPassword extends Field
{

    public function __construct($row)
    {
        parent::__construct($row);
        if (empty($this->help)) {
            $this->help = 'Оставьте пустым, если не требуется изменить';
        }
    }

    public function input($value)
    {
        $in = $this->inputName();

        return <<<HTML
<div class="form-control form-control--pass form-control--sm">
                <input name="$in"
                       value=""
                       placeholder="Оставьте пустым, если не требуется изменить"
                       type="password"
                       class="form-control__input">
                <a href="#" class="form-control-show-pass"></a>
            </div>
HTML;
    }

    public function getPOST($simple = false, $group = null)
    {
        $pw = $_REQUEST[$this->name];
        if (!$pw) {
            return $pw;
        }

        if ($_REQUEST[$this->tablePk] > 2) {
            $pw = crypt($pw, '$6$' . Str::random(16) . '$');
        } else {
            $pw = md5($pw);
        }

        return $pw;
    }

}

<?php

namespace Simflex\Admin\Fields;

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

    public function input($value): string
    {
        return $this->render('password', ['name' => $this->inputName()]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
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

    public static function typeLabel(): string
    {
        return 'Пароль';
    }

    public static function typeParams(): array
    {
        return [];
    }
}

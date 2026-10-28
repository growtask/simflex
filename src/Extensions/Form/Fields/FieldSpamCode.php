<?php

namespace Simflex\Core\Extensions\Form\Fields;

use Simflex\Core\System\Session;

class FieldSpamCode extends Field
{
    public array|int $vRequired = 1;

    public function html(): string
    {
        return '<span class="plug-form-spamcode"></span>';
    }

    public function &check(): array
    {
        $err = [];
        $value = (string)($this->getPosted() ?? '');
        if ($value === '' || $value !== Session::get('spamcode')) {
            $err[$this->name][] = 'спам-код введен неверно';
        }
        return $err;
    }
}

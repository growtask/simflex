<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\Helpers\Str;

class FieldAlias extends Field
{
    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $val = (string)$this->getPosted();
        if (empty($val) && !empty($this->params['source'])) {
            $val = Str::translite((string)$this->getPosted($this->params['source']));
        }
        return $this->e2n && $val === '' ? null : $val;
    }

    public function defval(): string
    {
        return '';
    }
}

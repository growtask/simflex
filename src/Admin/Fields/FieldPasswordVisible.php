<?php

namespace Simflex\Admin\Fields;

class FieldPasswordVisible extends Field
{
    public function __construct($row)
    {
        parent::__construct($row);
        if (empty($this->help)) {
            $this->help = 'При изменении записи оставьте поле пустым, если не требуется изменять';
        }
    }

    public function show($row): string
    {
        return $this->render('password-visible');
    }
}

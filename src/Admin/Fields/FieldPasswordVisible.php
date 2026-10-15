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

    public function show($row): void
    {
        echo '<div style="text-align: center">***</div>';
    }

    public static function typeLabel(): string
    {
        return 'Пароль видимый';
    }

    public static function typeParams(): array
    {
        return [];
    }
}

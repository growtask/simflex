<?php

namespace Simflex\Admin\Fields;

class ForeignKey
{
    public string $table = '';
    public string $key = '';
    public string $label = '';
    public bool $isPid = false;

    public function __construct($params)
    {
        $this->table = $params['fk_table'];
        $this->key = $params['fk_key'];
        $this->label = $params['fk_label'];
        $this->isPid = !empty($params['fk_is_pid']);
    }
}

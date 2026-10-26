<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldTags extends Field
{
    public array $values = [];

    public function input($value): string
    {
        if (!$this->values) {
            // загрузка данных
            $q = DB::query('SELECT DISTINCT ' . $this->name . ' FROM ' . $this->table);
            while ($r = DB::fetch($q)) {
                if (!$r[$this->name]) {
                    continue;
                }

                foreach (explode(',', $r[$this->name]) as $i) {
                    $this->values[] = $i;
                }

                $this->values = array_unique($this->values);
            }
        }

        return $this->render('tags', [
            'readonly' => false,
            'ajax' => false,
            'addNew' => true,
            'name' => $this->name,
            'value' => $value,
            'items' => array_map(fn($v) => [$v, $v], $this->values),
        ]);
    }
}

<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\DB\AQ;
use Simflex\Core\DB\DB;

class FieldTags extends Field
{
    public array $values = [];

    public function input($value): string
    {
        if (!$this->values) {
            // загрузка данных
            $query = new AQ()->select('DISTINCT ' . DB::wrapName($this->name))->from($this->table);
            foreach ($query->fetchColumn() as $tags) {
                if ($tags) {
                    array_push($this->values, ...explode(',', (string)$tags));
                }
            }
            $this->values = array_values(array_unique($this->values));
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

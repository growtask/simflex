<?php
namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

class FieldTags extends Field
{
    public  $values = [];

            public function input($value)
            {
                if (!$this->values) {
                    // load shit
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
                echo '<div class="form-control__tags" data-readonly="false">
<input class="form-control__tags-value" value="'.$value.'" type="hidden" name="'.$this->name.'">
<ul class="form-control__tags-list">
    <input type="text" class="form-control__tags-input">
</ul>
<div class="form-control__tags-popup list1">
    <div class="list1__wrapper">
        <ul class="list1__items">
        <li class="list1__item list1__item--add-new" data-value="" style="display: none"><strong>Добавить <span></span></strong></li>
';

                foreach ($this->values as $row) {
                    echo '<li data-value="'.$row.'" class="list1__item">' .$row. '</li>';
                }

                echo ' </ul>
        </div>
    </div>
</div>';
            }
}
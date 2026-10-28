<?php

namespace Simflex\Core\Admin\Fields;

class Helper
{
    public static function create($row): Field
    {
        $class = $row['class'];
        if (strpos($class, '\\') === false) {
            $class = "Simflex\Core\Admin\Fields\\$class";
        }
        return new $class($row);
    }
}

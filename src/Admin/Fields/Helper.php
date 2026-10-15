<?php


namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;

class Helper
{
    public static function create($row): Field
    {
        $class = $row['class'];
        if (strpos($class, '\\') === false) {
            $class = "Simflex\Admin\Fields\\$class";
        }
        return new $class($row);
    }
}
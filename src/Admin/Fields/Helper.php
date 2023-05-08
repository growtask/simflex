<?php


namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\Field;

class Helper
{
    /**
     * @param $row
     * @return Field
     */
    public static function create($row)
    {
        $class = $row['class'];
        if (strpos($class, '\\') === false) {
            $class = "Simflex\Admin\Fields\\$class";
        }
        return new $class($row);
    }
}
<?php
namespace Simflex\Admin\Fields;

use Simflex\Admin\Fields\Field;
use Simflex\Admin\Structure\Repository as StructureRepository;

class FieldTypeSelect extends Field
{
    public function input($value)
    {
        $out = '<select name="' . $this->inputName() . '" onchange="' . $this->onchange . '" class="form-control__input">';
        $out .= '<option value=""></option>';
        foreach (StructureRepository::fieldTypes() as $type) {
            $class = $type['class'];
            $selected = $class === $value ? ' selected' : '';
            $out .= '<option value="' . htmlspecialchars($class) . '"' . $selected . '>' . htmlspecialchars($type['name']) . '</option>';
        }
        return $out .'</select>';
    }

    public function getPOST($simple = false, $group = null)
    {
        return $simple && $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
    }
}

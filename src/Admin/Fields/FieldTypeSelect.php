<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Structure\Repository as StructureRepository;

class FieldTypeSelect extends Field
{
    public function input($value): string
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

    public function show($row): void
    {
        echo '<div class="table__row-' . $this->name . ' table__row-text">' . htmlspecialchars($this->typeName((string)$row[$this->name])) . '</div>';
    }

    public function showDetail($row): ?string
    {
        return htmlspecialchars($this->typeName((string)$row[$this->name]));
    }

    protected function typeName(string $class): string
    {
        if ($class === '') {
            return '';
        }

        $type = StructureRepository::fieldTypeByClass($class);
        return $type['name'] ?? $class;
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        return $simple && $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
    }
}

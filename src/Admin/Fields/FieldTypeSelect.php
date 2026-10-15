<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Structure\Repository as StructureRepository;

class FieldTypeSelect extends Field
{
    public function input($value): string
    {
        $options = [['', '', false]];
        foreach (StructureRepository::fieldTypes() as $type) {
            $options[] = [$type['class'], $type['name'], $type['class'] === $value];
        }
        return $this->render('select', [
            'name' => $this->inputName(),
            'onchange' => $this->onchange,
            'class' => 'form-control__input',
            'multiple' => false,
            'wrap' => false,
            'options' => $options,
        ]);
    }

    public function show($row): string
    {
        return $this->cell(htmlspecialchars($this->typeName((string)$row[$this->name])));
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

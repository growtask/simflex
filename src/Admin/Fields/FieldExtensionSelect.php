<?php

namespace Simflex\Admin\Fields;

/**
 * Base for the fields choosing a value from a registry in code (classes, privileges, tables...),
 * see \Simflex\Core\ExtensionRegistry. Rendered as the same custom dropdown as a foreign key
 */
abstract class FieldExtensionSelect extends Field
{
    /**
     * @return array<string, string> value => name
     */
    abstract protected function options(): array;

    public function input($value): string
    {
        $value = ltrim((string)($this->value ?: $value), '\\');
        $options = $this->options();
        $items = [['', '—']];
        foreach ($options as $optionValue => $name) {
            $items[] = [$optionValue, $name];
        }

        return $this->dropdown([
            'name' => $this->inputName(),
            'value' => $value,
            'onchange' => $this->onchange,
            'readonly' => $this->readonly,
            'disabled' => $this->readonly,
            'current' => $options[$value] ?? '—',
            'items' => $items,
        ]);
    }

    public function filter($value): string
    {
        if (!$this->filter) {
            return '';
        }

        $options = $this->options();
        $items = [['', $this->label]];
        foreach ($options as $optionValue => $name) {
            $items[] = [$optionValue, $name];
        }

        return $this->dropdown([
            'name' => 'filter[' . $this->name . ']',
            'value' => (string)$value,
            'current' => $options[$value] ?? $this->label,
            'items' => $items,
        ]);
    }

    public function show($row): string
    {
        return $this->cell((string)$this->showDetail($row));
    }

    public function showDetail($row): ?string
    {
        $value = ltrim((string)($row[$this->name] ?? ''), '\\');
        return htmlspecialchars($this->options()[$value] ?? $value);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = $simple && $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        return $value === '' && !$simple && $this->e2n ? null : $value;
    }
}

<?php

namespace Simflex\Admin\Fields;

/**
 * Base for the fields choosing a class from the registry in code, see \Simflex\Core\ExtensionRegistry
 */
abstract class FieldExtensionSelect extends Field
{
    /**
     * @return array<string, string> class => name
     */
    abstract protected function options(): array;

    public function input($value): string
    {
        $value = (string)($this->value ?: $value);
        $out = '<select name="' . $this->inputName() . '" onchange="' . $this->onchange . '" class="form-control__input">';
        $out .= '<option value=""></option>';
        foreach ($this->options() as $class => $name) {
            $selected = $class === ltrim($value, '\\') ? ' selected' : '';
            $out .= '<option value="' . htmlspecialchars($class) . '"' . $selected . '>' . htmlspecialchars($name) . '</option>';
        }
        return $out . '</select>';
    }

    public function filter($value): void
    {
        if (!$this->filter) {
            return;
        }

        echo '<div class="form-control form-control--sm"><select name="filter[' . $this->name . ']" class="form-control__input">';
        echo '<option value="">' . htmlspecialchars($this->label) . '</option>';
        foreach ($this->options() as $class => $name) {
            $selected = $class === $value ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($class) . '"' . $selected . '>' . htmlspecialchars($name) . '</option>';
        }
        echo '</select></div>';
    }

    public function show($row): void
    {
        echo '<div class="table__row-' . $this->name . ' table__row-text">' . $this->showDetail($row) . '</div>';
    }

    public function showDetail($row): ?string
    {
        $class = ltrim((string)($row[$this->name] ?? ''), '\\');
        return htmlspecialchars($this->options()[$class] ?? $class);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $value = $simple && $group !== null ? $_POST[$group][$this->name] : $_POST[$this->name];
        return $value === '' && !$simple && $this->e2n ? null : $value;
    }
}

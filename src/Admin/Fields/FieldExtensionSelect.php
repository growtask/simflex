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
        $options = [['', '', false]];
        foreach ($this->options() as $class => $name) {
            $options[] = [$class, $name, $class === ltrim($value, '\\')];
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

    public function filter($value): void
    {
        if (!$this->filter) {
            return;
        }

        $options = [['', $this->label, false]];
        foreach ($this->options() as $class => $name) {
            $options[] = [$class, $name, $class === $value];
        }
        echo $this->render('select', [
            'name' => 'filter[' . $this->name . ']',
            'onchange' => null,
            'class' => 'form-control__input',
            'multiple' => false,
            'wrap' => true,
            'options' => $options,
        ]);
    }

    public function show($row): void
    {
        echo $this->cell((string)$this->showDetail($row));
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

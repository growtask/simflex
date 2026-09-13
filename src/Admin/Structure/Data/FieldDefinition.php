<?php

namespace Simflex\Admin\Structure\Data;

class FieldDefinition
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $label = null,
        public readonly ?string $class = null,
        public readonly int $npp = 500,
        public readonly string $help = '',
        public readonly string $placeholder = '',
        public readonly array $params = [],
    ) {
    }

    public static function fromArray(array $data, ?string $name = null): self
    {
        return new self(
            name: $data['name'] ?? $name,
            label: $data['label'] ?? null,
            class: $data['class'] ?? $data['type'] ?? null,
            npp: (int)($data['npp'] ?? 500),
            help: (string)($data['help'] ?? ''),
            placeholder: (string)($data['placeholder'] ?? ''),
            params: $data['params'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'class' => $this->class,
            'npp' => $this->npp,
            'help' => $this->help,
            'placeholder' => $this->placeholder,
            'params' => $this->params,
        ];
    }
}

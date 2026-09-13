<?php

namespace Simflex\Admin\Structure\Data;

class FieldDefinition
{
    /**
     * @param array{main?: FieldParams} $params
     */
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly ?string $label = null,
        public readonly string $help = '',
        public readonly string $placeholder = '',
        public readonly array $params = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'class' => $this->class,
            'help' => $this->help,
            'placeholder' => $this->placeholder,
            'params' => ['main' => ($this->params['main'] ?? new FieldParams())->toArray()],
        ];
    }
}

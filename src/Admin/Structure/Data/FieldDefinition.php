<?php

namespace Simflex\Admin\Structure\Data;

readonly class FieldDefinition
{
    /**
     * @param array{main?: FieldParams} $params
     */
    public function __construct(
        public string $name,
        public string $class,
        public ?string $label = null,
        public string $help = '',
        public string $placeholder = '',
        public array $params = [],
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

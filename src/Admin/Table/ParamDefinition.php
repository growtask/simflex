<?php

namespace Simflex\Admin\Table;

class ParamDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $label = null,
        public readonly ?string $class = null,
        public readonly int|string|null $paramId = null,
        public readonly int|string|null $paramPid = '',
        public readonly string $pos = 'left',
        public readonly string $defaultValue = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'class' => $this->class,
            'param_id' => $this->paramId,
            'param_pid' => $this->paramPid,
            'pos' => $this->pos,
            'default_value' => $this->defaultValue,
            'params' => (new FieldDefinition($this->name, (string)$this->class, defaultValue: $this->defaultValue))->toArray()['params'],
        ];
    }
}

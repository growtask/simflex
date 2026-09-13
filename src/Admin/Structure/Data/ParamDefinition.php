<?php

namespace Simflex\Admin\Structure\Data;

class ParamDefinition
{
    /**
     * @param array{main?: FieldParams} $params
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $label = null,
        public readonly ?string $class = null,
        public readonly int|string|null $paramId = null,
        public readonly int|string|null $paramPid = '',
        public readonly string $pos = 'left',
        public readonly string $defaultValue = '',
        public readonly array $params = [],
    ) {
    }

    public static function fromArray(array $data, ?string $name = null): self
    {
        return new self(
            name: $data['name'] ?? $name,
            label: $data['label'] ?? null,
            class: $data['class'] ?? $data['type'] ?? null,
            paramId: $data['param_id'] ?? null,
            paramPid: $data['param_pid'] ?? '',
            pos: (string)($data['pos'] ?? 'left'),
            defaultValue: (string)($data['default_value'] ?? ''),
            params: $data['params'] ?? [],
        );
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
            'params' => ['main' => ($this->params['main'] ?? new FieldParams())->toArray()],
        ];
    }
}

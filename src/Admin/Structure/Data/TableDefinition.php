<?php

namespace Simflex\Admin\Structure\Data;

class TableDefinition
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly string $orderBy = '',
        public readonly bool $orderDesc = false,
        public readonly int|string|null $privAdd = null,
        public readonly int|string|null $privEdit = null,
        public readonly int|string|null $privDelete = null,
        public readonly string $class = '',
        public readonly array $fields = [],
        public readonly array $params = [],
    ) {
    }

    public static function fromArray(array $data, ?string $name = null): self
    {
        return new self(
            name: $data['name'] ?? $name,
            orderBy: (string)($data['order_by'] ?? ''),
            orderDesc: (bool)($data['order_desc'] ?? false),
            privAdd: $data['priv_add'] ?? null,
            privEdit: $data['priv_edit'] ?? null,
            privDelete: $data['priv_delete'] ?? null,
            class: (string)($data['class'] ?? ''),
            fields: static::fieldsFromArray($data['fields'] ?? []),
            params: static::paramsFromArray($data['params'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'order_by' => $this->orderBy,
            'order_desc' => $this->orderDesc,
            'priv_add' => $this->privAdd,
            'priv_edit' => $this->privEdit,
            'priv_delete' => $this->privDelete,
            'class' => $this->class,
            'fields' => $this->definitionsToArray($this->fields),
            'params' => $this->definitionsToArray($this->params),
        ];
    }

    private static function fieldsFromArray(array $fields): array
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $fields[$key] = FieldDefinition::fromArray($field, is_string($key) ? $key : null);
            }
        }

        return $fields;
    }

    private static function paramsFromArray(array $params): array
    {
        foreach ($params as $key => $param) {
            if (is_array($param)) {
                $params[$key] = ParamDefinition::fromArray($param, is_string($key) ? $key : null);
            }
        }

        return $params;
    }

    private function definitionsToArray(array $definitions): array
    {
        foreach ($definitions as $key => $definition) {
            if ($definition instanceof FieldDefinition || $definition instanceof ParamDefinition) {
                $definitions[$key] = $definition->toArray();
            }
        }

        return $definitions;
    }
}

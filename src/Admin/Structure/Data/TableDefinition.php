<?php

namespace Simflex\Admin\Structure\Data;

class TableDefinition
{
    /**
     * @param FieldDefinition[] $fields
     * @param ParamDefinition[] $params
     * @param string $class Custom admin driver class (extends \Simflex\Admin\Base) for tables that need
     *                       non-generic behaviour, e.g. \Simflex\Extensions\Content\Admin\AdminContent::class.
     *                       Most tables leave this empty and get the generic \Simflex\Admin\Base driver.
     */
    public function __construct(
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

    public function toArray(): array
    {
        return [
            'order_by' => $this->orderBy,
            'order_desc' => $this->orderDesc,
            'priv_add' => $this->privAdd,
            'priv_edit' => $this->privEdit,
            'priv_delete' => $this->privDelete,
            'class' => $this->class,
            'fields' => $this->fields,
            'params' => $this->params,
        ];
    }
}

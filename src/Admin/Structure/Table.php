<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamDefinition;

abstract class Table
{
    /**
     * @param FieldDefinition[] $fields
     * @param ParamDefinition[] $params
     * @param string|null $privilegeAdd, $privilegeEdit, $privilegeDelete Privilege names (\Simflex\Core\Privilege) required to add, edit, delete rows
     * @param string $class Custom admin driver class (extends \Simflex\Admin\Base) for tables that need
     *                       non-generic behaviour, e.g. \Simflex\Extensions\Content\Admin\AdminContent::class.
     *                       Most tables leave this empty and get the generic \Simflex\Admin\Base driver.
     */
    public function __construct(
        public readonly string $orderBy = '',
        public readonly bool $orderDesc = false,
        public readonly ?string $privilegeAdd = null,
        public readonly ?string $privilegeEdit = null,
        public readonly ?string $privilegeDelete = null,
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
            'privilege_add' => $this->privilegeAdd,
            'privilege_edit' => $this->privilegeEdit,
            'privilege_delete' => $this->privilegeDelete,
            'class' => $this->class,
            'fields' => $this->fields,
            'params' => $this->params,
        ];
    }
}

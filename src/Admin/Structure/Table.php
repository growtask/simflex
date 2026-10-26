<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamDefinition;

/**
 * Table section: the list and the form of the rows of a database table. The table name is the key
 * in Admin/Structure/handlers.php
 */
abstract class Table extends Handler
{
    /**
     * @param FieldDefinition[] $fields
     * @param ParamDefinition[] $params
     * @param string|null $privilegeAdd, $privilegeEdit, $privilegeDelete Privilege names (\Simflex\Core\Privilege) required to add, edit, delete rows
     * @param string $class See Handler::__construct()
     */
    public function __construct(
        public readonly string $orderBy = '',
        public readonly bool $orderDesc = false,
        public readonly ?string $privilegeAdd = null,
        public readonly ?string $privilegeEdit = null,
        public readonly ?string $privilegeDelete = null,
        string $class = '',
        public readonly array $fields = [],
        public readonly array $params = [],
    ) {
        parent::__construct($class);
    }

    public function toArray(): array
    {
        return parent::toArray() + [
            'order_by' => $this->orderBy,
            'order_desc' => $this->orderDesc,
            'privilege_add' => $this->privilegeAdd,
            'privilege_edit' => $this->privilegeEdit,
            'privilege_delete' => $this->privilegeDelete,
            'fields' => $this->fields,
            'params' => $this->params,
        ];
    }
}

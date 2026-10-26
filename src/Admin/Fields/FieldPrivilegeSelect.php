<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\Privilege;

/**
 * Имя привилегии из кода, см. \Simflex\Core\Privilege
 */
class FieldPrivilegeSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        $options = [];
        foreach (Privilege::getAll() as $name => $params) {
            $options[$name] = "$name — $params->title";
        }
        return $options;
    }

    public function showDetail($row): ?string
    {
        return htmlspecialchars((string)($row[$this->name] ?? ''));
    }
}

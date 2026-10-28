<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\System\Privilege;

/**
 * Имя привилегии из кода, см. \Simflex\Core\System\Privilege
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

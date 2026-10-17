<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\Privilege;

/**
 * Privilege name from code, see \Simflex\Core\Privilege
 */
class FieldPrivilegeSelect extends FieldExtensionSelect
{
    public static function typeLabel(): string
    {
        return 'Выбор привилегии';
    }

    protected function options(): array
    {
        $options = [];
        foreach (Privilege::all() as $name => $params) {
            $options[$name] = "$name — $params->title";
        }
        return $options;
    }

    public function showDetail($row): ?string
    {
        return htmlspecialchars((string)($row[$this->name] ?? ''));
    }
}

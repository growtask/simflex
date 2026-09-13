<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Structure\Repository;

/**
 * Table name from the admin tables registry (Admin/Structure/tables.php), e.g. admin_menu.model
 */
class FieldTableSelect extends FieldExtensionSelect
{
    public static function typeLabel(): string
    {
        return 'Выбор таблицы админки';
    }

    protected function options(): array
    {
        $out = [];
        foreach (Repository::tableClasses() as $name => $class) {
            $out[$name] = $name;
        }
        ksort($out);
        return $out;
    }
}

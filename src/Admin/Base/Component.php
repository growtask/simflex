<?php

namespace Simflex\Admin\Base;

use Simflex\Admin\Base;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Structure\Repository as StructureRepository;
use Simflex\Core\DB;

/**
 * Отвечает за редактирование компонента в админке
 */
class Component extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function tableParamsInit(): void
    {
        parent::tableParamsInit();
        $componentId = (int)@$_GET['component_id'];
        if (!$componentId) {
            return;
        }

        $component = DB::result('select class, params from component where component_id = ?', '', [$componentId]);
        if (!$component) {
            return;
        }
        $tableParams = unserialize($component['params']) ?: [];
        $rows = StructureRepository::extensionParams($component['class'], $this->table);
        if (!count($rows)) {
            return;
        }

        $hasWithoutGroup = false;
        foreach ($rows[''] as $pid => $row) {
            if (!isset($rows[$pid]) && $row['class']) {
                $hasWithoutGroup = true;
                break;
            }
        }

        if ($hasWithoutGroup) {
            $fields = [];
            foreach ($rows[''] as $row) {
                if ($row['class']) {
                    $field = Helper::create($row);
                    $field->value = $tableParams[$row['name']] ?? $field->defaultValue;
                    $fields[] = $field;
                }
            }
            $group = ['name' => '', 'label' => 'Параметры'];
            $group['fields'] = $fields;
            $this->params['right'][-1] = $group;
        }

        $counter = -2;
        foreach ($rows[''] as $pid => $row0) {
            $fields = [];
            if (!isset($rows[$pid])) {
                continue;
            }
            $group = $row0;
            foreach ($rows[$pid] as $row) {
                $field = new $row['class']($row);
                $field->form = $row0['name'];
                $field->value = $tableParams[$row['name']] ?? $field->defaultValue;
                $fields[] = $field;
            }
            $group['fields'] = $fields;
            $this->params['right'][$counter--] = $group;
        }
    }

    protected function getParams(): array
    {
        $params = parent::getParams();
        if (!empty($_POST['component_id'])) {
            $componentId = (int)$_POST['component_id'];
            $class = DB::result('select class from component where component_id = ?', '', [$componentId]);
            $rows = StructureRepository::extensionParams($class ?: '', $this->table);
            $groups = $rows[''] ?? [];
            foreach ($rows as $pid => $pidRows) {
                foreach ($pidRows as $row) {
                    if (!$row['class']) {
                        continue;
                    }
                    $field = new $row['class']($row);
                    if ($pid !== '') {
                        $groupName = $groups[$pid]['name'];
                        $value = $field->getPost(true, $groupName);
                        $value = preg_replace("@^'(.+)'$@", '$1', $value);
                        $params[$groupName][$field->name] = $value;
                    } else {
                        $value = $field->getPost(true);
                        $value = preg_replace("@^'(.+)'$@", '$1', $value);
                        $params[$field->name] = $value;
                    }
                }
            }
        }
        return $params;
    }
}

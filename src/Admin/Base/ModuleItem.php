<?php

namespace Simflex\Admin\Base;

use Simflex\Admin\Base;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Page;
use Simflex\Admin\Plugins\Editor\Editor;
use Simflex\Admin\Structure\Repository as StructureRepository;
use Simflex\Core\DB;

class ModuleItem extends Base
{
    public function __construct()
    {
        parent::__construct();
        Page::coreJs('/Base/js/moduleitem.js');
        $this->actionHandlers['module_param'] = ['method' => 'moduleParam'];
    }

    protected function moduleParam(): void
    {
        $moduleId = (int)@$_GET['module_id'];
        $itemId = (int)@$_GET['item_id'];

        $q = "SELECT * FROM module WHERE module_id = $moduleId";
        $module = DB::result($q);

        $menuTable = $module['type'] == 'site' ? 'menu' : 'admin_menu';
        $q = "SELECT menu_id, menu_pid pid, name FROM $menuTable ORDER BY npp, menu_id";
        $menuItems = DB::assoc($q);

        $rows = StructureRepository::extensionParams($module['class'], $this->table);
        $tableParams = $itemId
            ? (unserialize((string)DB::result('SELECT params FROM module_item WHERE item_id = ?', 'params', [$itemId])) ?: [])
            : [];
        if (count($rows)) {

            $positions = ['left' => '', 'right' => ''];
            $editorInited = false;

            $hasWithoutGroup = false;
            foreach ($rows[''] as $pid => $row) {
                if (!isset($rows[$pid]) && $row['class']) {
                    $hasWithoutGroup = true;
                    break;
                }
            }

            if ($hasWithoutGroup) {
                foreach ($rows[''] as $row) {
                    if ($row['class']) {
                        $field = Helper::create($row);
                        $field->value = $tableParams[$row['name']] ?? $field->defaultValue;
                        $fields[] = $field;
                    }
                }
                $group = ['label' => 'Параметры'];
                $group['fields'] = $fields;
                $portletClass = 'field-params-nogroup';
                ob_start();
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    Editor::tinymce('full');
                    $editorInited = true;
                }
                include 'tpl/form.portlet.tpl';
                $positions['right'] .= ob_get_clean();
            }

            foreach ($rows[''] as $pid => $row0) {
                $fields = [];
                if (!isset($rows[$pid])) {
                    continue;
                }
                foreach ($rows[$pid] as $row) {
                    $field = new $row['class']($row);
                    $field->value = $tableParams[$row['name']] ?? $field->defaultValue;
                    $fields[] = $field;
                }

                $group = ['label' => $row0['label']];
                $group['fields'] = $fields;
                $portletClass = "field-params-{$row0['name']}";
                ob_start();
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    Editor::tinymce('full');
                    $editorInited = true;
                }
                include 'tpl/form.portlet.tpl';
                $positions[$row0['position']] .= ob_get_clean();
            }
        }

        $menuOptions = ['<option value=""></option>'];
        foreach ($menuItems as $mitem) {
            $span = $mitem['pid'] ? str_repeat('&nbsp;', 4) : '';
            $menuOptions[] = '<option value="' . $mitem['menu_id'] . '">' . $span . htmlspecialchars($mitem['name']) . '</option>';
        }
        $positions['menu_items'] = implode("\n", $menuOptions);

        echo json_encode($positions);
    }

    protected function getParams(): array
    {
        $params = parent::getParams();
        if (!empty($_POST['module_id'])) {
            $moduleId = (int)$_POST['module_id'];
            $class = DB::result('select class from module where module_id = ?', '', [$moduleId]);
            foreach (StructureRepository::extensionFields($class ?: '', $this->table) as $row) {
                $field = new $row['class']($row);
                $value = $field->getPost(true);
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params[$field->name] = $value;
            }
        }
        return $params;
    }
}

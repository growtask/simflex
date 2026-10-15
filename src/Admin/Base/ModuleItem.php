<?php

namespace Simflex\Admin\Base;

use Simflex\Admin\Base;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Page;
use Simflex\Admin\Plugins\Editor\Editor;
use Simflex\Admin\Structure\Repository as StructureRepository;
use Simflex\Core\DB;
use Simflex\Core\ExtensionRegistry;
use Simflex\Core\Models\ModuleItem as ModuleItemModel;
use Simflex\Core\ModuleType;

class ModuleItem extends Base
{
    public function __construct()
    {
        parent::__construct();
        Page::coreJs('/Base/js/moduleitem.js');
        $this->actionHandlers['module_param'] = ['method' => 'moduleParam'];
    }

    protected function moduleParam(): string
    {
        $moduleClass = (string)@$_GET['module'];
        $itemId = (int)@$_GET['item_id'];

        $module = ExtensionRegistry::module($moduleClass);
        $moduleClass = $module ? ltrim($moduleClass, '\\') : '';

        $menuTable = ($module['type'] ?? ModuleType::Site) === ModuleType::Site ? 'menu' : 'admin_menu';
        $q = "SELECT menu_id, menu_pid pid, name FROM $menuTable ORDER BY npp, menu_id";
        $menuItems = DB::assoc($q);

        $rows = StructureRepository::extensionParams($moduleClass, $this->table);
        $tableParams = $itemId
            ? (ModuleItemModel::findOne(['item_id' => $itemId])?->getParams() ?: [])
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
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    $positions['right'] .= Editor::tinymce('full');
                    $editorInited = true;
                }
                $positions['right'] .= $this->render(__DIR__ . '/tpl/form.portlet.tpl', get_defined_vars());
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
                if (!empty($field->params['editor_full']) && !$editorInited) {
                    $positions[$row0['position']] .= Editor::tinymce('full');
                    $editorInited = true;
                }
                $positions[$row0['position']] .= $this->render(__DIR__ . '/tpl/form.portlet.tpl', get_defined_vars());
            }
        }

        $menuOptions = ['<option value=""></option>'];
        foreach ($menuItems as $mitem) {
            $span = $mitem['pid'] ? str_repeat('&nbsp;', 4) : '';
            $menuOptions[] = '<option value="' . $mitem['menu_id'] . '">' . $span . htmlspecialchars($mitem['name']) . '</option>';
        }
        $positions['menu_items'] = implode("\n", $menuOptions);

        return json_encode($positions);
    }

    protected function getParams(): array
    {
        $params = parent::getParams();
        if (!empty($_POST['module'])) {
            $class = ExtensionRegistry::module((string)$_POST['module']) ? ltrim((string)$_POST['module'], '\\') : '';
            foreach (StructureRepository::extensionFields($class, $this->table) as $row) {
                $field = new $row['class']($row);
                $value = $field->getPost(true);
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params[$field->name] = $value;
            }
        }
        return $params;
    }
}

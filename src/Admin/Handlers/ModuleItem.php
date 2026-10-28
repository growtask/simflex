<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldModuleSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Page;
use Simflex\Admin\Plugins\Editor\Editor;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\ParamDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\ForeignKeyField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\NppField;
use Simflex\Admin\Table\Repository;
use Simflex\Admin\Table\Table;
use Simflex\Core\ExtensionRegistry;
use Simflex\Models\ModuleItem as ModuleItemModel;

/**
 * Модули, размещённые на страницах сайта. Настройки модуля (ExtensionBase::adminParams()) загружаются в форму
 * через ajax при выборе модуля и хранятся в params
 */
class ModuleItem extends Table
{
    protected static string $model = ModuleItemModel::class;
    protected static ?string $defaultOrderBy = 'npp';

    public static function getFields(): array
    {
        return [
            new ActiveField(),
            new IdField(
                name: 'item_id',
                width: '80',
                filter: true,
                widthMob: '40',
            ),
            new NppField(),
            new FieldDefinition(
                name: 'name',
                class: FieldString::class,
                label: 'Наименование',
                width: '1',
                widthMob: '200',
            ),
            new FieldDefinition(
                name: 'posname',
                class: FieldString::class,
                label: 'Позиция',
                width: '200',
                filter: true,
            ),
            new FieldDefinition(
                name: 'module',
                class: FieldModuleSelect::class,
                label: 'Модуль',
                width: '200',
                filter: true,
                onchange: 'onChangeModule(this)',
            ),
            new ForeignKeyField(
                name: 'menu_id',
                label: 'Меню',
                table: 'menu',
                width: '200',
            ),
            new FieldDefinition(
                name: 'params',
                class: FieldText::class,
                label: 'Параметры',
                hidden: true,
            ),
        ];
    }

    public static function getParams(): array
    {
        return [
            new ParamDefinition(
                name: 'module_item_main',
                label: 'Параметры',
                paramId: 27,
                paramPid: '',
                pos: 'right',
            ),
            new ParamDefinition(
                name: 'is_title',
                label: 'Показывать заголовок',
                class: FieldBool::class,
                paramId: 28,
                paramPid: '',
                pos: 'right',
            ),
            new ParamDefinition(
                name: 'is_wrap',
                label: 'Выводить обертку',
                class: FieldBool::class,
                paramId: 29,
                paramPid: '',
                pos: 'right',
            ),
            new ParamDefinition(
                name: 'cssclass',
                label: 'CSS Класс',
                class: FieldString::class,
                paramId: 30,
                paramPid: '',
                pos: 'right',
            ),
        ];
    }

    public function __construct()
    {
        parent::__construct();
        Page::coreJs('/Table/js/moduleitem.js');
        $this->actionHandlers['module_param'] = ['method' => 'moduleParam'];
    }

    protected function moduleParam(): string
    {
        $moduleClass = (string)@$_GET['module'];
        $itemId = (int)@$_GET['item_id'];

        $module = ExtensionRegistry::module($moduleClass);
        $moduleClass = $module ? ltrim($moduleClass, '\\') : '';

        $rows = Repository::getExtensionParams($moduleClass, $this->table);
        $tableParams = $itemId
            ? (ModuleItemModel::findOne(['item_id' => $itemId])?->getParams() ?: [])
            : [];
        $positions = ['left' => '', 'right' => ''];
        if (count($rows)) {
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
                $positions['right'] .= $this->render(__DIR__ . '/../Table/tpl/form.portlet.tpl', get_defined_vars());
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
                $positions[$row0['position']] .= $this->render(__DIR__ . '/../Table/tpl/form.portlet.tpl', get_defined_vars());
            }
        }

        return json_encode($positions);
    }

    protected function getPostedParams(): array
    {
        $params = parent::getPostedParams();
        if (!empty($_POST['module'])) {
            $class = ExtensionRegistry::module((string)$_POST['module']) ? ltrim((string)$_POST['module'], '\\') : '';
            // поля модуля загружаются в форму через ajax: неотправленные сохраняют прежние значения
            $itemId = (int)($_REQUEST['item_id'] ?? 0);
            $saved = $itemId ? (ModuleItemModel::findOne(['item_id' => $itemId])?->getParams() ?: []) : [];
            foreach (Repository::getExtensionFields($class, $this->table) as $row) {
                $field = new $row['class']($row);
                if (!array_key_exists($field->name, $_POST)) {
                    if (array_key_exists($field->name, $saved)) {
                        $params[$field->name] = $saved[$field->name];
                    }
                    continue;
                }
                $value = $field->getPost(true);
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params[$field->name] = $value;
            }
        }
        return $params;
    }
}

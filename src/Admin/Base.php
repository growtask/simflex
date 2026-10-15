<?php

namespace Simflex\Admin;

use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Fields\FieldAlias;
use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldFile;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Core;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldPath;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldVirtual;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Structure\Repository as StructureRepository;
use Simflex\Admin\Plugins\Alert\Alert;

use Simflex\Admin\Plugins\Pagecontrol\Pagecontrol;
use Simflex\Core\DB;
use Simflex\Admin\Fields\Field;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Core\File;
use Simflex\Core\Image;
use Simflex\Core\Service;
use Simflex\Core\Time;
use Simflex\Core\User;

class Base
{

    public string $name = '';
    protected string $action = '';
    protected string $table = '';
    protected array $tableData = [];  // table metadata
    protected array $fields = [];     // fields by name
    protected array $fks = [];        // forieng key fields
    protected array $blocks = [];     // form field group labels, filled in by form.tpl
    protected FieldInt|false $pk = false;
    protected FieldInt|false $pid = false;
    protected bool $isHierarchy = false;
    protected array $params = ['left' => [], 'right' => []];
    protected array $where = [];
    protected array $whereOr = [];
    protected array $where_sys = [];
    protected string $order = '';
    protected string $order2 = '';
    protected int $desc = 0;
    protected array $errors = [];
    protected int $p = 0;
    protected int $p_on = 20;
    protected int $p_count = 0;
    protected bool $is_filter = false;
    static protected bool $is_init = false;
    protected array $actionHandlers = [
        '' => ['method' => 'show'],
        'form' => ['method' => 'form'],
        'save' => ['method' => 'save'],
        'delete' => ['method' => 'delete'],
        'validate' => ['method' => 'validate'],
        'bool' => ['method' => 'bool'],
        'change_enum' => ['method' => 'changeENUM'],
        'change_npp' => ['method' => 'changeNPP'],
        'delete_field' => ['method' => 'deleteField'],
        'upload_img' => ['method' => 'uploadImg'],
        'upload_file' => ['method' => 'uploadFile'],
        'showDetail' => ['method' => 'showDetail'],
        'searchTags' => ['method' => 'searchTags'],
    ];
    protected array|false $row = false;
    public static string $currentWhere = '';
    public static bool $isGroup = false;
    public static bool $isAdd = false;
    public static bool $isEdit = false;
    protected bool $canAdd = false;
    protected bool $canCopy = false;
    protected bool $canEdit = false;
    protected bool $canEditGroup = false;
    protected bool $canDelete = false;
    public string $title = '';
    public bool $selectAll = false;
    public array $selectAdditional = [];
    public string $extraRight = '';
    public string $extraLeft = '';

    /**
     * Количество кнопок у каждой строки в списке записей (метод show). Влияет на ширину колонки
     * Необязательно задавать, пересчитывается на JS
     */
    protected int $rowActionsCnt = 1;

    /**
     *
     * @var string ID записей при групповом редактировании (через запятую)
     */
    public string $ids;

    public function __construct()
    {
        $this->title = Core::menuCurItem('name') ?? '';
        $this->ids = isset($_GET['ids']) ? $_GET['ids'] : '';
        if (isset($_POST['group_ids'])) {
            $this->ids = $_POST['group_ids'];
        }

        // filter this
        $this->ids = implode(',', array_filter(array_map(fn ($val) => intval($val), explode(',', $this->ids))));

        $this->initTableName();
        $this->initTableData();

        $this->canAdd = !empty($this->tableData['priv_add']) ? User::ican((int)$this->tableData['priv_add']) : true;
        $this->canCopy = $this->canAdd;
        $this->canEdit = !empty($this->tableData['priv_edit']) ? User::ican((int)$this->tableData['priv_edit']) : true;
        $this->canEditGroup = $this->canEdit;
        $this->canDelete = !empty($this->tableData['priv_delete']) ? User::ican((int)$this->tableData['priv_delete']) : true;
    }

    protected function initTableName(): void
    {
        if (empty($this->table)) {
            $row = Core::menuCurItem();
            if (!empty($row['model'])) {
                $this->table = $row['model'];
            }
        }
        $this->name = $this->table;
    }

    protected function initTableData(): void
    {
        $this->tableData = StructureRepository::table($this->table);
    }

    protected function deleteField(): void
    {
        $fieldName = DB::escape(@$_GET['field_name']);
        $keyName = $this->pk->name;
        $keyValue = (int)$_REQUEST[$keyName];
        if (!isset($this->fields[$fieldName])) {
            // must be custom thing.
            // TODO: do this properly!
            $data = DB::result('SELECT params FROM `' . $this->table . "` WHERE $keyName = $keyValue");
            if (!$data) {
                exit(json_encode(['success' => false]));
            }

            $data = unserialize($data['params']);
            $data[$fieldName] = '';
            $data = serialize($data);

            DB::query("UPDATE $this->table SET params = ? WHERE $keyName = $keyValue", [$data]);
            exit(
            json_encode(['success' => true, 'd' => $data, 't' => $this->table, 'k' => $keyName, 'v' => $keyValue])
            );
        }
        $field = $this->fields[$fieldName];
        $q = "select $field->name from `$this->table` where $keyName = $keyValue";
        $row = DB::result($q);
        $success = $field->delete($row[$field->name]);
        if ($success) {
            $q = "UPDATE " . $this->table . " SET " . $field->name . "='' WHERE $keyName = $keyValue";
            $success = DB::query($q);
        }
        echo json_encode(['success' => !!$success]);
    }

    public function uploadImg(): never
    {
        $img = new Image($_REQUEST['path']);
        $img->loadPost('img');
        $name = $img->getName();
        $img->save();

        die($name);
    }

    public function uploadFile(): never
    {
        $f = new File();
        $f->loadPost('file');
        $name = $f->getName();
        $f->save();

        die($name);
    }

    public function searchTags(): never
    {
        /** @var Field $field */
        $field = $this->fields[$_REQUEST['name']];

        $q = DB::query(
            "select {$this->pk->name}, {$field->params['name']} from {$this->table} where {$field->params['name']} like ? limit 50",
            [
                '%' . $_REQUEST['text'] . '%'
            ]
        );

        $out = [];
        while ($r = DB::fetch($q)) {
            $out[] = ['id' => $r[$this->pk->name], 'name' => $r[$field->params['name']]];
        }

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    public function content(): void
    {
        $row = Core::menuCurItem();

        if (!empty($row['model'])) {
            $this->initTable();

            $action = isset($_GET['action']) ? $_GET['action'] : '';

            if ($action == 'save') {
                if ($keyValue = $this->save()) {
                    if (isset($_REQUEST['submit_apply'])) {
                        $href = './?action=form&' . $this->pk->name . '=' . $keyValue;
                        header('location: ' . $href);
                    } elseif (isset($_REQUEST['submit_save_add'])) {
                        header('location: ./?action=form');
                    } else {
                        header("location: ./?save_id=$keyValue");
                    }
                    exit();
                }
            } else {
                if (isset($this->actionHandlers[$action])) {
                    $method = $this->actionHandlers[$action]['method'];
                    if (method_exists($this, $method)) {
                        $this->$method();
                    } else {
                        echo Core::siteParam('error404');
                    }
                } elseif (method_exists($this, $action)) {
                    $this->$action();
                } else {
                    echo Core::siteParam('error404');
                }
            }
        } else {
            $this->widgets();
            $this->index();
        }
    }

    protected function widgets(): void
    {
        $q = "SELECT * FROM admin_widget WHERE active = 1 ORDER BY npp, widget_id";
        $rows = DB::assoc($q);
        echo count($rows) ? '<div class="widgets">' : '';
        foreach ($rows as $row) {
            if (!$row['priv_id'] || User::ican((int)$row['priv_id'])) {
                $class = $row['class'];
                $widget = new $class($row);
                echo $widget->execute();
            }
        }
        echo count($rows) ? '</div>' : '';
    }

    protected function beforeChangeENUM(Field $field, $row, $newValue): mixed
    {
        if (!$this->canEdit) {
            Alert::error("В этом разделе запрещено редактировать записи", './');
        }
        if ($field->readonly) {
            Alert::error("Поле <b>{$field->label}</b> только для чтения!", './');
        }
        return $newValue;
    }

    protected function afterChangeENUM(Field $field, $row, $newValue, $success): void
    {
    }

    protected function changeENUM(bool $withRedirect = true): void
    {
        $fieldName = $_REQUEST['field'];
        $newValue = $_REQUEST['newstatus'];
        $field = $this->fields[$fieldName] ?? null;
        if (!$field) {
            Alert::error("Некорректный запрос. Поле не найдено.", './');
        }

        $id = (int)$_REQUEST[$this->pk->name];
        $q = "SELECT * FROM `$this->table` WHERE {$this->pk->name} = $id";
        $row = DB::result($q);

        $newValue = $this->beforeChangeENUM($field, $row, $newValue);

        $new = $newValue ? "'$newValue'" : 'null';
        $q = "UPDATE `$this->table` SET `$field->name` = $new WHERE {$this->pk->name} = $id";
        $success = DB::query($q);

        $this->afterChangeENUM($field, $row, $newValue, $success);

        if ($withRedirect) {
            exit(json_encode(['success' => true]));
        }
    }

    protected function changeNPP(): never
    {
        $field = DB::escape($_REQUEST['field']);

        $fieldDB = StructureRepository::field($this->table, $field);
        $fieldParams = $fieldDB['params'] ?? [];
        if (@$fieldParams['main']['readonly']) {
            Alert::error("Поле <b>{$fieldDB['label']}</b> только для чтения!", './');
        }

        if (isset($_GET['val'])) {
            $inc = (int)$_GET['val'];
            $id = (int)$_REQUEST[$this->pk->name];
            $q = "UPDATE `$this->table` SET `$field` = $inc WHERE {$this->pk->name} = $id";
            DB::query($q);
        } else {
            $inc = isset($_GET['up']) ? '-1' : '+1';
            $id = (int)$_REQUEST[$this->pk->name];
            $q = "UPDATE `$this->table` SET `$field` = `$field` $inc WHERE {$this->pk->name} = $id";
            DB::query($q);
        }

        header('location: ./');
        exit;
    }

    private function index(): void
    {
        $cur = Core::menuCurItem();
        $cur_id = isset($cur['menu_id']) ? $cur['menu_id'] : 0;
        $menu = Core::menu();
        if (isset($menu[$cur_id])) {
            include dirname(__FILE__) . '/Base/tpl/index.tpl';
        }
    }

    public final function execute(bool $is_print = false): string|false|null
    {
        if ($is_print) {
            $this->content();
        } else {
            ob_start();
            $this->content();
            return ob_get_clean();
        }
    }

    protected function getSelectSelect(): string
    {
        $ar = [];
        foreach ($this->fks as $field) {
            $fkAdd = '' . $field->fk->table . $field->name . '.' . $field->fk->label . ' ' . $field->name . '_label';
            if ($field->fk->table == $this->table) {
                $fkAdd = '(SELECT ' . $field->fk->label . ' FROM `' . $field->fk->table . '` t_' . $this->table . ' WHERE ' . $this->pk->name . ' = ' . $this->table . '.' . $field->name . ') ' . $field->name . '_label';
            }
            $ar[] = '' . $this->table . '.' . $field->name;
            $ar[] = $fkAdd;
        }
        foreach ($this->fields as $field) {
            if ($field->isVisible && !isset($this->fks[$field->name])) {
                $selectField = $field->selectQueryField();
                if ($selectField) {
                    $ar[] = $selectField;
                }
            }
        }

        foreach ($this->selectAdditional as $i) {
            $ar[] = $i;
        }

        $ret = "SELECT " . ($this->selectAll ? '*' : join(', ', $ar));
        return $ret;
    }

    protected function getSelectFrom(): string
    {
        $q = "\r\nFROM `" . $this->table . '`';
        foreach ($this->fks as $field) {
            if ($field->fk->table != $this->table) {
                $q .= "\r\n" . ($field->isnull ? "LEFT " : "") . "JOIN " . $field->fk->table . " " . $field->fk->table . $field->name . " ON " . $field->fk->table . $field->name . "." . $field->fk->key . "=" . $this->table . "." . $field->name;
            }
        }
        return $q;
    }

    protected function getSelectWhere(): string
    {
        $where = array_merge($this->where_sys, $this->where);
        $ret = count($where) ? "\r\nWHERE " . join(' AND ', $where) : '';
        self::$currentWhere = $ret;
        return $ret;
    }

    protected function getSelectOrderBy(): string
    {
        $order = [];
        if ($this->order) {
            $order[] = $this->order . ($this->desc ? ' DESC' : '');
        }
        if ($this->order2) {
            $order[] = $this->order2;
        }
        return count($order) ? "\r\nORDER BY " . join(', ', $order) : "";
    }

    protected function getSelectLimit(): string
    {
        return $this->isHierarchy ? "" : "\r\nLIMIT " . $this->p * $this->p_on . ", " . $this->p_on;
    }

    protected function getQueryCount(): string
    {
        $q = "SELECT COUNT(*) cnt";
        $q .= $this->getSelectFrom();
        $q .= $this->getSelectWhere();
        return $q;
    }

    protected function getQuerySelect(): string
    {
        $q = $this->getSelectSelect();
        $q .= $this->getSelectFrom();
        $q .= $this->getSelectWhere();
        $q .= $this->getSelectOrderBy();
        $q .= $this->getSelectLimit();
        return $q;
    }

    protected function filterReset(): never
    {
        $_SESSION[$this->table] = [];
        header('Location: ./');
        exit;
    }

    protected function prepareWhere(): void
    {
        foreach ($this->fields as $field) {
            $_SESSION[$this->table]['filter'][$field->name] = isset($_SESSION[$this->table]['filter'][$field->name]) ? $_SESSION[$this->table]['filter'][$field->name] : '';
            $_SESSION[$this->table]['filter'][$field->name] = isset($_REQUEST['filter'][$field->name]) ? $_REQUEST['filter'][$field->name] : $_SESSION[$this->table]['filter'][$field->name];
            if ($_SESSION[$this->table]['filter'][$field->name] !== '') {
                if ($_SESSION[$this->table]['filter'][$field->name] === 'null') {
                    $this->where[] = '' . $this->table . '.' . $field->name . " IS NULL";
                } else {
                    $this->isHierarchy = false;
                    if ($field instanceof FieldDateTime) {
                        $this->where[] = '' . $this->table . '.' . $field->name . " like '"
                            . Time::mysql($_SESSION[$this->table]['filter'][$field->name]) . "%'";
                    } elseif ($field instanceof FieldMultiKey) {
                        $fval = DB::escape($_SESSION[$this->table]['filter'][$field->name]);
                        $this->where[] = "(select count(*) from {$field->params['table_relations']} rls
                         where $this->table.{$this->pk->name} = rls.{$this->pk->name} and rls.{$field->params['key']} = $fval) > 0";
                    } else {
                        $fval = DB::escape($_SESSION[$this->table]['filter'][$field->name]);
                        if ($field instanceof FieldString) {
                            $this->where[] = "$this->table.$field->name like '%$fval%'";
                        } else {
                            $this->where[] = "$this->table.$field->name " . (strpos(
                                    $fval,
                                    '%'
                                ) === false ? '=' : 'like') . " '$fval'";
                        }
                    }
                }
            }
        }
    }

    public function show(): void
    {
        $this->prepareWhere();


        $q = $this->getQueryCount();
        $cnt = DB::result($q, 'cnt');
        if ($cnt > 200 && $this->isHierarchy) {
            $this->isHierarchy = false;
            if ($_SESSION[$this->table]['filter'][$this->pid->name] !== '') {
                $this->isHierarchy = false;
                $this->where[] = '' . $this->table . '.' . $this->pid->name . "='"
                    . DB::escape($_SESSION[$this->table]['filter'][$this->pid->name]) . "'";
                $q = $this->getQueryCount();
                $cnt = DB::result($q, 'cnt');
            }
        }

        $this->p_on = $this->isHierarchy ? 200 : $this->p_on;
        $pagecontrol = new Pagecontrol($this->p, $this->p_on, $cnt);

        $q = $this->getQuerySelect();
        $r = DB::query($q);
        $rows = [];
        if ($this->isHierarchy) {
            $tree_name = $this->pid->name;
            foreach ($this->fields as $field) {
                if ($field->isVisible && $field instanceof FieldString) {
                    $tree_name = $field->name;
                    break;
                }
            }

            $tree = [];
            while ($row = DB::fetch($r)) {
                $tree[(int)$row[$this->pid->name]][(int)$row[$this->pk->name]] = $row;
            }
            $id_start = isset($_SESSION[$this->table]['filter'][$this->pid->name]) ? (int)$_SESSION[$this->table]['filter'][$this->pid->name] : 0;
            $list = Service::tree2list($tree, $id_start);
            foreach ($list as $l) {
                if ($l['tree_level']) {
                    $l[$tree_name] = '<div style="padding-left:' . (25 * $l['tree_level']) . 'px">' . $l[$tree_name] . '</div>';
                }
                $rows[] = $l;
            }
        } else {
            while ($row = DB::fetch($r)) {
                $rows[] = $row;
            }
        }

        $saveId = (int)@$_GET['save_id'];

        include dirname(__FILE__) . '/Base/tpl/show.tpl';
    }

    /**
     * Use echo
     */
    protected function showCell(Field $field, array $row): void
    {
        echo $field->show($row);
    }

    protected function initTable(): void
    {
        if ($this->tableData) {
            // TABLE STRUCTURE
            $fields = StructureRepository::fields($this->table);
            $pkName = '';
            foreach ($fields as $field) {
                if (!empty($field['params']['main']['filter'])) {
                    $this->is_filter = true;
                }
                if (!empty($field['params']['main']['pk'])) {
                    $pkName = $field['name'];
                }

                $this->addField(Helper::create($field));
            }

            foreach ($this->fields as $field) {
                $field->tablePk = $pkName;
            }

            //TABLE PARAMS
            $this->tableParamsInit();
        }

        $this->action = isset($_GET['action']) ? $_GET['action'] : '';
        isset($_GET['p']) ? $this->p = abs($_GET['p']) : null;

        // ORDER SETTINGS
        if (!self::$is_init) {
            self::$is_init = true;
            if (isset($_REQUEST['o']) && isset($this->fields[$_REQUEST['o']])) {
                $o = $_REQUEST['o'];
                if (isset($_SESSION[$this->table]['order_by']) && $_SESSION[$this->table]['order_by'] == $o) {
                    $_SESSION[$this->table]['order_desc'] = ($_SESSION[$this->table]['order_desc'] + 1) % 2;
                } else {
                    $_SESSION[$this->table]['order_by'] = $o;
                    $_SESSION[$this->table]['order_desc'] = 0;
                }
            }
            $this->order = isset($_SESSION[$this->table]['order_by']) ? $_SESSION[$this->table]['order_by'] : $this->order;
            $this->desc = isset($_SESSION[$this->table]['order_desc']) ? $_SESSION[$this->table]['order_desc'] : $this->desc;
            if (empty($this->order)) {
                if (!empty($this->tableData['order_by'])) {
                    $this->order = $this->tableData['order_by'];
                    $this->desc = (int)($this->tableData['order_desc'] ?? 0);
                } else {
                    $this->order = $this->order2;
                    $this->order2 = '';
                }
            }
            $_SESSION[$this->table]['order_by'] = $this->order;
            $_SESSION[$this->table]['order_desc'] = $this->desc;
        }
    }

    /**
     * Загрузка параметров из БД
     */
    protected function tableParamsLoad(): array
    {
        return StructureRepository::params($this->table);
    }

    /**
     * Инициализация параметров. Загружаются из $this->tableParamsLoad()
     */
    protected function tableParamsInit(): void
    {
        $params = $this->tableParamsLoad();
        if (count($params)) {
            foreach ($params[''] as $group_id => $group) {
                $this->params[$group['pos']][$group_id] = $group;
                $this->params[$group['pos']][$group_id]['fields'] = [];

                if (isset($params[$group_id])) {
                    foreach ($params[$group_id] as $param) {
                        $field = Helper::create($param);
                        $field->form = $group['name'];
                        if ($dv = $param['default_value']) {
                            $field->defaultValue = $dv;
                        }
                        $this->params[$group['pos']][$group_id]['fields'][$param['name']] = $field;
                    }
                } elseif ($group['class']) {
                    $field = Helper::create($group);
                    if ($dv = $group['default_value']) {
                        $field->defaultValue = $dv;
                    }
                    $this->params[$group['pos']][$group_id]['field'] = $field;
                }
            }
        }
    }

    protected function filter(): void
    {
        if ($this->is_filter) {
            include dirname(__FILE__) . '/Base/tpl/filter.tpl';
        }
    }

    protected function filterExtra(bool $addClearfix = true): void
    {
        include 'Base/tpl/filter.extra.tpl';
    }

    protected function filterField(Field $field): void
    {
        $field->filter($_REQUEST['filter'][$field->name] ?? '');
    }

    public function addField($field): void
    {
        if ($field instanceof Field) {
            $this->fields[$field->name] = &$field;

            if ($field->pk) {
                $this->pk = &$field;
                $this->order2 = $field->name;
            }
            if ($field->fk) {
                $this->fks[$field->name] = &$field;
                if ($field->fk->table == $this->table) {
                    $this->pid = &$field;
                    $this->isHierarchy = true;
                }
            }

            /* SYSTEM FILTER */
            if ($field->name == 'priv_id' && !User::ican('dev')) {
                if ($field->isnull) {
                    $this->where_sys[] = "(" . $this->table . ".priv_id IN(" . join(',', User::privIds())
                        . ") OR " . $this->table . ".priv_id IS NULL)";
                } else {
                    $this->where_sys[] = "" . $this->table . ".priv_id IN(" . join(',', User::privIds()) . ")";
                }
            }
            if ($field->name == 'role_id' && !User::ican('dev')) {
                $this->where_sys[] = "" . $this->table
                    . ".role_id IN(SELECT role_id FROM user_role WHERE priv_id IN(" . join(',', User::privIds()) . "))";
            }
        }
    }

    public function boolChange(): mixed
    {
        $pk = (int)$_REQUEST['pk'];
        $field = $_REQUEST['field'];
        if ($pk && isset($this->fields[$field])) {
            $fieldDB = StructureRepository::field($this->table, $field);
            $fieldParams = $fieldDB['params'] ?? [];
            if (!@$fieldParams['main']['readonly']) {
                $q = "UPDATE " . $this->table . " SET $field=($field+1)%2 WHERE " . $this->pk->name . "=" . $pk;
                DB::query($q);
            }

            $q = "SELECT $field val FROM `" . $this->table . "` WHERE " . $this->pk->name . "=" . $pk;
            $row = DB::result($q);
            if (isset($row['val'])) {
                echo $row['val'] ? '1' : '0';
                return $row['val'];
            }
        }
        echo 'error';
        return -1;
    }

    public function form(): void
    {
        if (isset($_GET['ids']) && count(explode(',', $_GET['ids'])) == 1) {
            header("location: ?action=form&{$this->pk->name}={$_GET['ids']}");
            exit;
        }

        $where = $this->where_sys;

        $title = 'Добавить запись';

        $isGroup = self::$isGroup = !empty($_GET['ids']);
        $isAdd = self::$isAdd = !$isGroup && empty($_REQUEST[$this->pk->name]);
        $isEdit = self::$isEdit = !$isGroup && !$isAdd;

        if ($isAdd && !$this->canAdd) {
            Alert::error('Ошибка! Недостаточно прав для добавления записей в данный раздел', './');
        }
        if ($isEdit && !$this->canEdit) {
            Alert::error('Ошибка! Недостаточно прав для редактирования записей в данном разделе', './');
        }

        $row = &$this->row;
        $row = [];
        foreach ($this->fields as $index => $field) {
            if ($field instanceof FieldVirtual) {
                unset($this->fields[$index]);
            }
        }
        foreach ($this->fields as $field) {
            $row[$field->name] = $field->defval();
            $field->loadUI(true);
        }

        foreach ($this->fks as $field) {
            $fv = &$_SESSION[$this->table]['filter'][$field->name];
            if (@$fv !== '' && @$fv !== null) {
                $row[$field->name] = (int)@$fv;
            }
        }

        if (!empty($_POST)) {
            $row = $_POST + $row;
        }

        $pk = 0;
        if (!empty($_REQUEST[$this->pk->name])) {
            $pk = (int)$_REQUEST[$this->pk->name];
            $where[] = $this->pk->name . "=" . $pk;
            $q = "SELECT * FROM `" . $this->table . "` WHERE " . join(' AND ', $where);
            if ($rrow = DB::result($q)) {
                $row = $rrow;
                if ($this->title) {
                    $title = $this->title;
                } else {
                    $title = 'Редактировать запись №' . $pk;
                    $rowName = '';

                    if (isset($row['title'])) {
                        $rowName = $row['title'];
                    } elseif (isset($row['name'])) {
                        $rowName = $row['name'];
                    } elseif (isset($row['label'])) {
                        $rowName = $row['label'];
                    }
                    if ($rowName) {
                        mb_strlen($rowName, 'utf8') > 50 ? $rowName = mb_substr($rowName, 0, 47, 'utf8') . '...' : null;
                        $title .= ' &mdash; &laquo;' . $rowName . '&raquo;';
                    }
                }
            } else {
                Alert::error('Ошибка! Запись не найдена или недостаточно прав для редактирования.', './');
            }
        }

        if ($isGroup) {
            $title = 'Редактирование нескольких записей';
            $ids = DB::escape($_GET['ids']);
            $q = "SELECT * FROM `$this->table` WHERE {$this->pk->name} in (" . $ids . ")";
            $rows = DB::assoc($q);
            foreach ($this->fields as $field) {
                $vars = [];
                foreach ($rows as $row0) {
                    $vars[$row0[$field->name]] = true;
                }
                if (count($vars) == 1) {
                    foreach ($vars as $var => $useless) {
                        $row[$field->name] = $var;
                    }
                }
            }

            $hasSetParams = false;
            foreach ($this->fields as $field) {
                if ($field->name == 'params') {
                    $hasSetParams = true;
                    break;
                }
            }

            if ($hasSetParams) {
                $params = [];
                foreach ($this->params as $paramz) {
                    foreach ($paramz as $group) {
                        if (isset($group['fields'])) {
                            foreach ($group['fields'] as $field) {
                                if (is_object($field)) {
                                    if ($group['name']) {
                                        $vars = [];
                                        foreach ($rows as $row0) {
                                            $rowParams = unserialize($row0['params']);
                                            @$vars[$rowParams[$group['name']][$field->name]] = true;
                                        }
                                        if (count($vars) == 1) {
                                            foreach ($vars as $var => $useless) {
                                                $params[$group['name']][$field->name] = $var;
                                            }
                                        }
                                    } else {
                                        $vars = [];
                                        foreach ($rows as $row0) {
                                            $rowParams = unserialize($row0['params']);
                                            $vars[$rowParams[$field->name]] = true;
                                        }
                                        if (count($vars) == 1) {
                                            foreach ($vars as $var => $useless) {
                                                $params[$field->name] = $var;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (isset($group['field'])) {
                            $vars = [];
                            foreach ($rows as $row0) {
                                $rowParams = unserialize($row0['params']);
                                $vars[$rowParams[$field->name]] = true;
                            }
                            if (count($vars) == 1) {
                                foreach ($vars as $var => $useless) {
                                    $params[$field->name] = $var;
                                }
                            }
                        }
                    }
                }
                $row['params'] = serialize($params);
            }
        }

        if ($this->table == 'content_template_param' && !$this->params['right']) {
            $this->params['right'] = [1];
        }

        if (Core::$isAjax) {
            if (!empty($_REQUEST['field']) && isset($this->fields[$_REQUEST['field']]) && $pk) {
                $field = $this->fields[$_REQUEST['field']];
                $q = "UPDATE `" . $this->table . "` SET " . $field->name . "='' WHERE " . join(' AND ', $where);
                DB::query($q);
                $field->delete($row[$field->name]);
                exit();
            }
        }

        $params = [];
        if (!empty($row['params'])) {
            $params = unserialize($row['params']);
        }

        $this->formBeforeOutput($row);
        include dirname(__FILE__) . '/Base/tpl/form.tpl';
    }

    protected function formBeforeOutput(array &$row): void
    {
    }

    /**
     * Вывод html-кода для ввода данных на форме
     */
    protected function formFieldInput(Field $field, array $row): string
    {
        return $field->input($field->value ?: @$row[$field->name]);
    }

    public function showActions(): void
    {
        include 'Base/tpl/show.actions.tpl';
    }

    public function rowActions($row, $i): void
    {
        include 'Base/tpl/show.actions.row.tpl';
    }

    public function searchInt(): array
    {
        $text = trim(DB::escape($_REQUEST['text']));
        $name = str_replace(['filter[', ']'], '', $_REQUEST['name']);
        $field = $this->fields[$name];
        if (!($field instanceof FieldInt) || !$field->params['is_fk']) {
            return [];
        }

        $out = [];
        $q = DB::query(
            "select `{$field->params['fk_key']}` as id, `{$field->params['fk_label']}` as name from 
                                      `{$field->params['fk_table']}` where {$field->params['fk_label']} like '%$text%' limit 50"
        );
        while ($r = DB::fetch($q)) {
            $out[] = $r;
        }

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    public function searchMultiKey(): array
    {
        $text = DB::escape($_REQUEST['text']);
        $name = str_replace(['filter[', ']'], '', $_REQUEST['name']);
        $field = $this->fields[$name];
        if (!($field instanceof FieldMultiKey)) {
            return [];
        }

        $out = [];
        $q = DB::query(
            "select `{$field->params['key']}` as id, `{$field->params['key_alias']}` as name from 
                                      `{$field->params['table_values']}` where {$field->params['key_alias']} like '%$text%' limit 50"
        );
        while ($r = DB::fetch($q)) {
            $out[] = $r;
        }

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    public function copy(): never
    {
        $q = "SELECT * FROM `$this->table` WHERE {$this->pk->name} in ($this->ids)";
        $rows = DB::assoc($q);
        $fieldNames = ['name', 'title', 'label'];
        foreach ($rows as $row) {
            $set = [];
            foreach ($row as $key => $value) {
                if ($key == $this->pk->name) {
                    continue;
                }
                if (in_array($key, $fieldNames)) {
                    $value = "Копия $value";
                }
                if ($value === null) {
                } else {
                    $set[] = "$key = '$value'";
                }
            }
            $q = "INSERT INTO `$this->table` SET " . implode(', ', $set);
            $success = DB::query($q);
            if ($success) {
                $insertID = DB::insertID();
                Alert::success("Запись №{$row[$this->pk->name]} скопирована в №$insertID");
            } else {
                Alert::error("Запись №{$row[$this->pk->name]} не скопирована!");
                Alert::error($q);
                Alert::error(DB::error());
            }
        }
        header("location: ./");
        exit;
    }

    public function validate(): int|bool
    {
        foreach ($this->fields as $field) {
            if ($errors = $field->check()) {
                foreach ($errors as $error) {
                    $this->errors[$field->label][] = $error;
                }
            }
        }
        if (Core::$isAjax) {
            $json = [];
            $json['valid'] = count($this->errors) ? 0 : 1;
            if (count($this->errors)) {
                foreach ($this->errors as $field => $errors) {
                    $json['errors'][$field] = join('<br />', $errors);
                }
            }
            echo json_encode($json);
            return $json['valid'];
        } else {
            return count($this->errors) ? false : true;
        }
    }

    public function saveGroup(): string
    {
        $success = true;
        $idList = array_values(array_unique(array_filter(array_map('intval', explode(',', (string)($_POST['group_ids'] ?? ''))))));
        $ids = implode(',', $idList);
        if (!$idList) {
            Alert::error('Не выбраны записи для сохранения!');
            return 'ids';
        }
        $pkName = DB::wrapName($this->pk->name);
        $idPlaceholders = implode(', ', array_fill(0, count($idList), '?'));

        $set = [];
        $setValues = [];
        foreach ($this->fields as $index => $field) {
            if (isset($_POST['set'][$field->name]) && !($field instanceof FieldVirtual)) {
                $set[] = DB::wrapName($field->name) . ' = ?';
                $setValues[] = $field->getPOST();
            }
        }

        $hasSetParams = false;
        foreach ($this->fields as $field) {
            if ($field->name == 'params' && isset($_POST['set_param'])) {
                $hasSetParams = true;
                break;
            }
        }

        if ($hasSetParams) {
            $q = "SELECT $pkName id, params FROM `$this->table` WHERE $pkName IN ($idPlaceholders)";
            $rows = DB::assoc($q, false, false, $idList) ?: [];
            foreach ($rows as $row) {
                $params = unserialize($row['params']);
                if (!is_array($params)) {
                    $params = [];
                }

                $MY_POST = $_POST;
                foreach ($this->params as $paramz) {
                    foreach ($paramz as $group) {
                        if (isset($group['fields'])) {
                            foreach ($group['fields'] as $field) {
                                if (isset($_POST['set_param'][$field->name]) && is_object($field)) {
                                    if ($group['name']) {
                                        $_POST = $MY_POST[$group['name']];
                                        $value = $field->getPost(true);
                                        $value = preg_replace("@^'(.+)'$@", '$1', $value);
                                        $params[$group['name']][$field->name] = $value;
                                    } else {
                                        $_POST = $MY_POST;
                                        $value = $field->getPost(true);
                                        $value = preg_replace("@^'(.+)'$@", '\1', $value);
                                        $params[$field->name] = $value;
                                    }
                                }
                            }
                        }
                        if (isset($group['field'])) {
                            $field = $group['field'];
                            $_POST = $MY_POST;
                            $value = $field->getPost(true);
                            $value = preg_replace("@^'(.+)'$@", '\1', $value);
                            $params[$field->name] = $value;
                        }
                    }
                }
                $_POST = $MY_POST;

                $q = "UPDATE `$this->table` SET params = ? WHERE $pkName = ?";
                $success = DB::query($q, [serialize($params), $row['id']]) !== false && $success;
            }
        }

        if (count($set)) {
            $q = "UPDATE `$this->table` SET " . implode(', ', $set) . " WHERE $pkName IN ($idPlaceholders)";
            $success = DB::query($q, array_merge($setValues, $idList)) !== false && $success;
        }

        if ($success) {
            Alert::success('Записи ' . $ids . ' успешно сохранены!');
        } else {
            Alert::error('Записи ' . $ids . ' не сохранены!');
        }
        return 'ids';
    }

    /**
     * @return string|int|false pkValue | false
     */
    public function save(): string|int|false
    {
        if (!empty($_POST['group_ids'])) {
            return $this->saveGroup();
        }

        if ($this->validate()) {
            foreach ($this->fields as $index => $field) {
                if ($field instanceof FieldVirtual) {
                    unset($this->fields[$index]);
                }
            }

            $isInsert = empty($_POST[$this->pk->name]);
            $isUpdate = !$isInsert;
            $q = $isInsert ? $this->getQueryInsert() : $this->getQueryUpdate();

            DB::query($q[0], $q[1]);
            if (DB::errno()) {
                $this->errors[] = "Введенные данные некорректны";
                $this->errors[] = DB::error();
            } else {
                if (empty($_POST[$this->pk->name])) {
                    $_POST[$this->pk->name] = DB::insertID();
                }
                $pkValue = $_POST[$this->pk->name];

                $field_alias = false;
                $field_path = false;
                foreach ($this->fields as $field) {
                    if ($field instanceof FieldAlias) {
                        $field_alias = $field;
                    }
                    if ($field instanceof FieldPath) {
                        $field_path = $field;
                    }
                }
                if ($field_path && $field_alias) {
                    if ($this->pid) {
                        $where = $this->where_sys;
                        $where[] = "" . $this->table . "." . $this->pk->name . "=" . (int)$_POST[$this->pk->name];
                        $q = "
                            SELECT " . $this->table . "." . $this->pk->name . ", " . $this->table . "." . $this->pid->name . ", " . $this->table . "." . $field_alias->name . ", t2." . $field_path->name . "
                            FROM `" . $this->table . "`
                            LEFT JOIN " . $this->table . " t2 ON t2." . $this->pk->name . " = " . $this->table . "." . $this->pid->name . "
                            WHERE " . join(" AND ", $where) . "
                        ";
                        $row = DB::result($q);
                        $path = $row['path'] ? $row['path'] . $row['alias'] . '/' : '/' . $row['alias'] . '/';
                        $path = preg_replace('@\/+@', '/', $path);
                        $row[$field_path->name] = $path;

                        $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path . "' WHERE " . join(
                                " AND ",
                                $where
                            );
                        DB::query($q);

                        $steck[] = $row;
                        $i = 0;
                        $counter = 0;
                        while ($i < count($steck)) {
                            $cur = $steck[$i];
                            $where = $this->where_sys;
                            $where[] = "" . $this->table . "." . $this->pid->name . "=" . $cur[$this->pk->name];
                            $q = "
                                SELECT " . $this->pk->name . ", " . $field_alias->name . ", " . $field_path->name . "
                                FROM `" . $this->table . "`
                                WHERE " . join(" AND ", $where) . "
                            ";
                            $rows = DB::assoc($q);
                            foreach ($rows as $row) {
                                if ($row[$field_path->name] != $cur[$field_path->name] . $row[$field_alias->name] . '/') {
                                    $counter++;
                                    $path = $cur[$field_path->name] . $row[$field_alias->name] . '/';
                                    $where = $this->where_sys;
                                    $where[] = "" . $this->table . "." . $this->pk->name . "=" . $row[$this->pk->name];
                                    $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path
                                        . "' WHERE " . join(" AND ", $where);
                                    DB::query($q);
                                    $row[$field_path->name] = $path;

                                    $steck[] = $row;
                                }
                            }
                            $i++;
                        }
                    } else {
                        $where = $this->where_sys;
                        $where[] = "" . $this->table . "." . $this->pk->name . "=" . (int)$_POST[$this->pk->name];
                        $path = '/' . $_POST[$field_alias->name] . '/';
                        $q = "UPDATE " . $this->table . " SET " . $field_path->name . "='" . $path . "' WHERE " . join(
                                " AND ",
                                $where
                            );
                        DB::query($q);
                    }
                }
            }
            if (count($this->errors)) {
                Alert::error('Ошибка при сохранении записи' . ($pkValue ? " №$pkValue" : ''));
                $this->form();
            } else {
                Alert::success('Запись №' . $pkValue . ' успешно сохранена!');
            }
        } else {
            $this->form();
        }

        return count($this->errors) ? false : $pkValue;
    }

    protected function getQueryInsert(): array
    {
        $keys = $values = [];
        $vals = [];
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            }
            $atq = !$field->isVirtual && !$field->readonly || $field instanceof FieldPath;
            $post = $field->getPOST();
            if ($atq) {
                if (empty($post) || $post == 'NULL' || $post == "''") {
                    $cinfo = DB::columnInfo($this->table, $field->name);
                    $atq = empty($cinfo['Default']);
                }
            }
            if ($atq) {
                $keys[] = DB::wrapName($field->name);
                if ($field->name == 'params') {
                    $params = $this->getParams();
                    $post = serialize($params);
                }
                $vals[] = $post;
                $values[] = '?';
            }
        }
        $q = "INSERT INTO `" . $this->table . "` (" . implode(', ', $keys) . ") VALUES (" . implode(
                ', ',
                $values
            ) . ")";
        return [$q, $vals];
    }

    protected function getQueryUpdate(): array
    {
        $q = "SELECT * FROM `" . $this->table . "` WHERE " . $this->pk->name . "=" . (int)$_POST[$this->pk->name];
        $row = DB::result($q);

        $q = "UPDATE `" . $this->table . "` SET";
        $vals = [];
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            } elseif (!$field->readonly) {
                if ($field->name == 'params') {
                    $params = $this->getParams();
                    $q .= " " . DB::wrapName($field->name) . "=?,";
                    $vals[] = serialize($params);
                } elseif ($field instanceof FieldPassword) {
                    $value = $field->getPOST();
                    if ($value) {
                        $q .= " " . DB::wrapName($field->name) . "=?,";
                        $vals[] = $value;
                    }
                } elseif ($field instanceof FieldDateTime) {
                    $cinfo = DB::columnInfo($this->table, $field->name);
                    if (empty($cinfo['Extra'])) {
                        $value = $field->getPOST();
                        // null — очистка поля (e2n), пустая строка — оставить как есть
                        if ($value !== '') {
                            $q .= " " . DB::wrapName($field->name) . "=?,";
                            $vals[] = $value;
                        }
                    }
                } elseif ($field instanceof FieldFile) {
                    if ($value = trim($field->getPOST(), "'")) {
                        $q .= " `" . $field->name . "`=?,";
                        $vals[] = $value;
                    }
                } else {
                    $value = $field->getPOST();
                    $q .= " " . DB::wrapName($field->name) . "=?,";
                    $vals[] = $value;
                }
            }
        }
        $q = substr($q, 0, -1);
        $q .= " WHERE " . $this->pk->name . "=" . (int)$_POST[$this->pk->name];

        return [$q, $vals];
    }

    protected function getParams(): array
    {
        $params = [];
        $MY_POST = $_POST;
        foreach ($this->params as $paramz) {
            foreach ($paramz as $group) {
                if (isset($group['fields'])) {
                    foreach ($group['fields'] as $field) {
                        if (is_object($field)) {
                            if ($group['name']) {
                                $_POST = $MY_POST[$group['name']];
                                $value = $field->getPost(true);
                                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                                $params[$group['name']][$field->name] = $value;
                            } else {
                                $_POST = $MY_POST;
                                $value = $field->getPost(true);
                                $value = preg_replace("@^'(.+)'$@", '\1', $value);
                                $params[$field->name] = $value;
                            }
                        }
                    }
                }
                if (isset($group['field'])) {
                    $field = $group['field'];
                    $_POST = $MY_POST;
                    $value = $field->getPost(true);
                    $value = preg_replace("@^'(.+)'$@", '\1', $value);
                    $params[$field->name] = $value;
                }
            }
        }
        $_POST = $MY_POST;
        return $params;
    }

    /**
     * Удаляет одну запись
     * @return string "errno: error" или пустая строка при успехе
     */
    protected function deleteItem(int $id): string
    {
        $where = $this->where_sys;
        $where[] = $this->pk->name . "=" . (int)$id;
        $q = "DELETE FROM `" . $this->table . "` WHERE " . join(' AND ', $where);
        DB::query($q);
        return DB::errno() ? DB::errno() . ': ' . DB::error() : '';
    }

    /**
     * Обработчик кнопки "удалить"
     */
    public function delete(): never
    {
        if (!$this->canDelete) {
            Alert::error('Ошибка! Недостаточно прав для удаления записей в данном разделе', './');
        }

        $ids_temp = (isset($_REQUEST['rows']) && is_array($_REQUEST['rows'])) ? $_REQUEST['rows'] : [];
        $ids = [];
        foreach ($ids_temp as $id) {
            if ((int)$id > 0) {
                $ids[] = (int)$id;
            }
        }

        foreach ($this->fields as $field) {
            if ($field instanceof FieldFile) {
                $dir = 'files';
                if ($field instanceof FieldImage) {
                    $dir = 'images';
                }
                $q = "SELECT " . $field->name . " FROM `" . $this->table . "` WHERE " . $this->pk->name . " IN (" . join(
                        ',',
                        $ids
                    ) . ")";
                $rows = DB::assoc($q);
                foreach ($rows as $row) {
                    if ($row[$field->name] && is_file($_SERVER['DOCUMENT_ROOT'] . '/uf/' . $dir . '/' . $field->path . $row[$field->name])) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/uf/' . $dir . '/' . $field->path . $row[$field->name]);
                        if (isset($field->sizes)) {
                            foreach ($field->sizes as $size) {
                                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/uf/' . $dir . '/' . $field->path . $size . '/' . $row[$field->name])) {
                                    unlink($_SERVER['DOCUMENT_ROOT'] . '/uf/' . $dir . '/' . $field->path . $size . '/' . $row[$field->name]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $deleted = [];
        foreach ($ids as $id) {
            $n = $this->deleteItem($id);
            if ($n) {
                Alert::error("#$id &mdash; Ошибка. Удаление записи не возможно");
                Alert::error("#$id &mdash; $n");
            } else {
                $deleted[] = $id;
                Alert::success("#$id &mdash; Успех. Запись удалена");
            }
        }
        header("location: ./");
        exit;
    }

    protected function portlets(string $position = 'right'): void
    {
    }

    protected function getRow(int|string $id): array|false|null
    {
        $id = (int)$id;
        $q = "SELECT * FROM `$this->table` WHERE {$this->pk->name} = $id";
        $row = DB::result($q);
        return $row;
    }


    protected function showDetailExtra($row): void
    {
    }

    public function showDetail(): never
    {
        $id = (int)@$_GET['id'];
        $row = $this->showDetailGetRow($id);
        include $this->showDetailGetTpl();
        exit;
    }

    protected function showDetailGetTpl(): string
    {
        return 'Base/tpl/show.detail.tpl';
    }

    protected function showDetailGetRow(int $id): array|false|null
    {
        $q = $this->getSelectSelect() . $this->getSelectFrom() . "\nWHERE `{$this->pk->name}` = $id";
        $row = DB::result($q);
        return $row;
    }

    /**
     * @throws \Exception code == 999 - skip row
     */
    protected function showDetailPrepareValue($row, $key): ?string
    {
        $value = $row[$key];
        /** Field $field */
        if (empty($field = &$this->fields[$key])) {
            throw new \Exception('Skip this field', 999);
        }
        if (is_string($value) && mb_strlen($value) > 1500) {
            $value = mb_substr($value, 0, 1500) . '...';
        }
        if ($key == 'params' && is_string($value) && substr($value, 0, 2) == 'a:') {
            $value = '<pre>' . print_r(unserialize($value), true) . '</pre>';
        } else {
            $value = $field->showDetail($row);
        }
        return $value;
    }
}

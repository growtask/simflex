<?php

namespace Simflex\Admin\Table;

use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Fields\FieldAlias;
use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldFile;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldPath;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldVirtual;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Core;
use Simflex\Admin\Handler;
use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Admin\Plugins\Pagination\Pagination;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\AQ;
use Simflex\Core\DB\Expr;
use Simflex\Core\DB\ExprOr;
use Simflex\Admin\Fields\Field;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Core\File;
use Simflex\Core\Image;
use Simflex\Core\ModelBase;
use Simflex\Core\Privilege;
use Simflex\Core\Helpers\Tree;
use Simflex\Core\Time;

/**
 * Табличный раздел: список и форма записей таблицы БД. Подкласс объявляет таблицу:
 * $model, getFields(), при необходимости getParams() и статические свойства ниже, и может менять поведение
 */
abstract class Table extends Handler
{
    /** @var class-string<ModelBase> Модель таблицы: имя таблицы, первичный ключ, запросы раздела. Обязательно */
    protected static string $model;

    /** Колонка сортировки списка, пока пользователь не выбрал другую. Null — первичный ключ */
    protected static ?string $defaultOrderBy = null;

    protected static bool $defaultOrderDesc = false;

    /** Имена привилегий (\Simflex\Core\Privilege) для добавления, изменения, удаления записей. Null — любой админ */
    protected static ?string $privilegeAdd = null;
    protected static ?string $privilegeEdit = null;
    protected static ?string $privilegeDelete = null;

    /** Записей на странице списка. Дерево (таблица с родителем) показывается на одной странице до 200 записей */
    protected static int $perPage = 20;

    /**
     * Каталог файлов, загружаемых в колонки image/file полей-таблиц (FieldTable, FieldVirtualTable), см.
     * uploadTableFile(): изображения — в /uf/images/<каталог>/, остальное — в /uf/files/<каталог>/.
     * Пусто — имя таблицы раздела
     */
    protected static string $uploadDir = '';

    /** @var string[] расширения изображений, которые можно загрузить в поле-таблицу */
    protected static array $uploadImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'];

    /** @var string[] расширения остальных файлов, которые можно загрузить в поле-таблицу */
    protected static array $uploadFileExtensions = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'rtf', 'txt', 'csv', 'zip', 'rar', '7z',
        'mp4', 'webm',
    ];

    /** Имя таблицы, из модели */
    protected string $table {
        get => static::getTableName();
    }
    /** @var array<string, Field> поля по имени */
    protected array $fields = [];
    /** @var array<string, Field> поля внешних ключей по имени */
    protected array $fks = [];
    protected ?FieldInt $pk = null;
    /** Родитель записи в той же таблице: список показывается деревом */
    protected ?FieldInt $pid = null;
    protected bool $isHierarchy = false;
    protected array $params = ['left' => [], 'right' => []];
    protected array $errors = [];
    /** По какому-то полю есть фильтр (FieldDefinition::$filter): список показывает панель фильтров */
    protected bool $isFilter = false;
    protected array $actionHandlers = [
        '' => ['method' => 'show'],
        'form' => ['method' => 'form'],
        'save' => ['method' => 'save'],
        'delete' => ['method' => 'delete'],
        'validate' => ['method' => 'validate'],
        'change_enum' => ['method' => 'changeENUM'],
        'change_npp' => ['method' => 'changeNPP'],
        'delete_field' => ['method' => 'deleteField'],
        'upload_img' => ['method' => 'uploadImg'],
        'upload_file' => ['method' => 'uploadFile'],
        'upload_table_file' => ['method' => 'uploadTableFile'],
        'showDetail' => ['method' => 'showDetail'],
        'searchTags' => ['method' => 'searchTags'],
    ];
    /** Запрос показываемого списка, для полей со связанными записями его записей (FieldMultiKey) */
    public static ?AQ $currentQuery = null;
    protected bool $canAdd = false;
    protected bool $canCopy = false;
    protected bool $canEdit = false;
    protected bool $canEditGroup = false;
    protected bool $canDelete = false;
    public bool $selectAll = false;
    public array $selectAdditional = [];
    /**
     * HTML, который раздел добавляет в форму под полями: в левую (основную) колонку и в правую.
     * Непустой $extraRight показывает правую колонку даже без полей в ней. Задаётся, например, в form()
     * до parent::form()
     */
    public string $extraLeft = '';
    public string $extraRight = '';

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
        parent::__construct();
        $this->ids = $_GET['ids'] ?? '';
        if (isset($_POST['group_ids'])) {
            $this->ids = $_POST['group_ids'];
        }

        // фильтруем это
        $this->ids = implode(',', array_filter(array_map(fn($val) => intval($val), explode(',', $this->ids))));

        $this->canAdd = static::allowed(static::$privilegeAdd);
        $this->canCopy = $this->canAdd;
        $this->canEdit = static::allowed(static::$privilegeEdit);
        $this->canEditGroup = $this->canEdit;
        $this->canDelete = static::allowed(static::$privilegeDelete);
    }

    /**
     * У текущего пользователя есть привилегия, нужная для действия, см. $privilegeAdd и остальные
     */
    protected static function allowed(?string $privilege): bool
    {
        return !$privilege || (Container::getUser()?->ican($privilege) ?? false);
    }

    /**
     * @return class-string<ModelBase>
     */
    public static function getModel(): string
    {
        if (!isset(static::$model)) {
            throw new \LogicException(static::class . ': declare the model of the table, protected static string $model');
        }

        return static::$model;
    }

    /**
     * Поля списка и формы, по порядку
     *
     * @return FieldDefinition[]
     */
    abstract public static function getFields(): array;

    /**
     * Параметры боковых панелей формы, хранятся сериализованными в колонке params
     *
     * @return ParamDefinition[]
     */
    public static function getParams(): array
    {
        return [];
    }

    public static function getTableName(): string
    {
        return static::getModel()::getTableName();
    }

    /**
     * Поля в виде записей, из которых создаются поля админки
     */
    public static function getFieldRows(): array
    {
        return array_map(fn(FieldDefinition $field) => Repository::toFieldRow($field, static::getTableName()), static::getFields());
    }

    public static function getFieldRow(string $name): array
    {
        foreach (static::getFieldRows() as $row) {
            if ($row['name'] === $name) {
                return $row;
            }
        }

        return [];
    }

    /**
     * Параметры в виде записей, из которых создаются поля админки
     *
     * @return array<string, array<string, array>> param_pid => param_id => запись
     */
    public static function getParamRows(): array
    {
        $out = [];
        foreach (static::getParams() as $param) {
            $row = Repository::toParamRow($param, static::getTableName());
            $out[(string)$row['param_pid']][(string)$row['param_id']] = $row;
        }

        return $out;
    }

    /**
     * Запрос записей таблицы в виде массивов, через модель
     */
    protected function getQuery(): AQ
    {
        return static::getModel()::findAdv()->asArray();
    }

    protected function deleteField(): string
    {
        $fieldName = (string)@$_GET['field_name'];
        $keyName = $this->pk->name;
        $keyValue = (int)$_REQUEST[$keyName];
        $where = [$keyName => $keyValue];
        if (!isset($this->fields[$fieldName])) {
            // поле из параметров
            $data = $this->getQuery()->select('params')->where($where)->fetchOne();
            if (!$data) {
                return json_encode(['success' => false]);
            }

            $data = unserialize($data['params']) ?: [];
            $data[$fieldName] = '';
            $data = serialize($data);

            static::getModel()::bulkUpdate(['params' => $data], $where);
            return json_encode(['success' => true, 'd' => $data, 't' => $this->table, 'k' => $keyName, 'v' => $keyValue]);
        }
        $field = $this->fields[$fieldName];
        $row = $this->getQuery()->select(DB::wrapName($field->name))->where($where)->fetchOne();
        $success = $field->delete($row[$field->name] ?? '');
        if ($success) {
            $success = static::getModel()::bulkUpdate([$field->name => ''], $where);
        }
        return json_encode(['success' => !!$success]);
    }

    /**
     * Ajax: сохраняет изображение ($_FILES['img']) в /uf/images/<path>/, возвращает имя файла (пустое — не принято).
     * Нужно право добавлять или менять записи; path — только буквы, цифры, «_», «-» и «/»
     */
    public function uploadImg(): never
    {
        $path = $this->getUploadPath((string)($_REQUEST['path'] ?? ''));
        if (!($this->canAdd || $this->canEdit) || $path === null) {
            die('');
        }

        $img = new Image($path);
        $img->loadPost('img');
        $name = $img->getName();
        $img->save();

        die($name);
    }

    /**
     * Ajax: сохраняет файл ($_FILES['file']) в /uf/files/, возвращает имя файла (пустое — не принято).
     * Нужно право добавлять или менять записи; расширения — $uploadImageExtensions и $uploadFileExtensions
     */
    public function uploadFile(): never
    {
        $ext = strtolower(pathinfo((string)($_FILES['file']['name'] ?? ''), PATHINFO_EXTENSION));
        $allowed = array_merge(static::$uploadImageExtensions, static::$uploadFileExtensions);
        if (!($this->canAdd || $this->canEdit) || !in_array($ext, $allowed, true)) {
            die('');
        }

        $f = new File();
        $f->loadPost('file');
        $name = $f->getName();
        $f->save();

        die($name);
    }

    /**
     * Подкаталог загрузки из запроса: null, если в нём есть «..» или недопустимые символы
     */
    protected function getUploadPath(string $path): ?string
    {
        $path = trim($path, '/');
        if (str_contains($path, '..') || !preg_match('@^[A-Za-z0-9_/-]*$@', $path)) {
            return null;
        }

        return $path === '' ? '' : $path . '/';
    }

    /**
     * Ajax: сохраняет файл ($_FILES['file']), загруженный в колонку image/file поля-таблицы (FieldTable,
     * FieldVirtualTable), возвращает его адрес. Принимаются файлы с расширениями из $uploadImageExtensions
     * (в /uf/images/<$uploadDir>/) и $uploadFileExtensions (в /uf/files/<$uploadDir>/), имя файла — случайное.
     * Пустая строка — файл не принят: нет прав на добавление и изменение записей, ошибка загрузки, другое расширение
     */
    public function uploadTableFile(): string
    {
        $file = $_FILES['file'] ?? null;
        if (!($this->canAdd || $this->canEdit) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return '';
        }

        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, static::$uploadImageExtensions, true)) {
            $type = 'images';
        } elseif (in_array($ext, static::$uploadFileExtensions, true)) {
            $type = 'files';
        } else {
            return '';
        }

        $dir = '/uf/' . $type . '/' . trim(static::$uploadDir ?: $this->table, '/') . '/';
        $path = $_SERVER['DOCUMENT_ROOT'] . $dir;
        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            return '';
        }

        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $path . $name)) {
            return '';
        }

        return $dir . $name;
    }

    public function searchTags(): never
    {
        /** @var Field $field */
        $field = $this->fields[$_REQUEST['name']];

        $name = $field->params['name'];
        $rows = $this->getQuery()
            ->select([DB::wrapName($this->pk->name), DB::wrapName($name)])
            ->where(DB::wrapName($name) . ' LIKE :text')
            ->bind(['text' => '%' . $_REQUEST['text'] . '%'])
            ->limit(50)
            ->all();

        $out = [];
        foreach ($rows as $r) {
            $out[] = ['id' => $r[$this->pk->name], 'name' => $r[$name]];
        }

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Содержимое раздела: результат запрошенного действия. Действия возвращают HTML
     * (или JSON для ajax-запросов)
     */
    public function content(): string
    {
        $this->initTable();

        $action = $_GET['action'] ?? '';

        if ($action == 'save') {
            if ($keyValue = $this->save()) {
                if (isset($_REQUEST['submit_apply'])) {
                    $href = Core::path() . '?action=form&' . $this->pk->name . '=' . $keyValue;
                    header('location: ' . $href);
                } elseif (isset($_REQUEST['submit_save_add'])) {
                    header('location: ' . Core::path() . '?action=form');
                } else {
                    header('location: ' . Core::path() . "?save_id=$keyValue");
                }
                exit();
            }
            // не сохранено: снова форма, с ошибками
            return $this->form();
        }

        $method = isset($this->actionHandlers[$action]) ? $this->actionHandlers[$action]['method'] : $action;
        if (!method_exists($this, $method)) {
            return (string)Core::siteParam('error404');
        }
        $out = $this->$method();
        return is_string($out) ? $out : '';
    }

    protected function beforeChangeENUM(Field $field, $row, $newValue): mixed
    {
        if (!$this->canEdit) {
            Alert::error('В этом разделе запрещено редактировать записи', Core::path());
        }
        if ($field->readonly) {
            Alert::error("Поле <b>{$field->label}</b> только для чтения!", Core::path());
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
            Alert::error('Некорректный запрос. Поле не найдено.', Core::path());
        }

        $where = [$this->pk->name => (int)$_REQUEST[$this->pk->name]];
        $row = $this->getQuery()->where($where)->fetchOne();

        $newValue = $this->beforeChangeENUM($field, $row, $newValue);

        $success = static::getModel()::bulkUpdate([$field->name => $newValue ?: null], $where);

        $this->afterChangeENUM($field, $row, $newValue, $success);

        if ($withRedirect) {
            exit(json_encode(['success' => true]));
        }
    }

    protected function changeNPP(): never
    {
        $field = (string)$_REQUEST['field'];
        if (!isset($this->fields[$field])) {
            Alert::error('Некорректный запрос. Поле не найдено.', Core::path());
        }

        $fieldDB = static::getFieldRow($field);
        $fieldParams = $fieldDB['params'] ?? [];
        if (@$fieldParams['main']['readonly']) {
            Alert::error("Поле <b>{$fieldDB['label']}</b> только для чтения!", Core::path());
        }

        $where = [$this->pk->name => (int)$_REQUEST[$this->pk->name]];
        if (isset($_GET['val'])) {
            $value = (int)$_GET['val'];
        } else {
            $value = (int)$this->getQuery()->select(DB::wrapName($field))->where($where)->fetchScalar();
            $value += isset($_GET['up']) ? -1 : 1;
        }
        static::getModel()::bulkUpdate([$field => $value], $where);

        header('location: ' . Core::path());
        exit;
    }

    /**
     * Колонка для SQL: `table`.`column`
     */
    protected function wrapColumn(string $column): string
    {
        return implode('.', array_map(fn(string $part) => DB::wrapName($part), explode('.', $column)));
    }

    /**
     * Записи, которые пользователь может видеть и менять: не разработчик — только записи своих привилегий
     * (колонка привилегии) и ролей этих привилегий (колонка роли).
     * В виде, который принимает \\Simflex\\Core\\DB\\Where: для чтения и записи
     */
    protected function getScope(): array
    {
        $user = Container::getUser();
        if ($user?->ican(Privilege::DEV)) {
            return [];
        }

        $privileges = array_values($user?->getPrivileges() ?? []) ?: [''];
        $scope = [];
        if (isset($this->fields['privilege'])) {
            $column = $this->table . '.privilege';
            $scope[] = $this->fields['privilege']->isnull
                ? new ExprOr([$column => $privileges, new Expr($this->wrapColumn($column) . ' IS NULL')])
                : new ExprOr([$column => $privileges]);
        }
        if (isset($this->fields['role_id'])) {
            $names = implode(',', array_map(fn(string $name) => DB::wrapString($name), $privileges));
            $scope[] = new Expr(
                $this->wrapColumn($this->table . '.role_id') . " IN (SELECT r.role_id FROM user_role r WHERE r.privilege IN ($names))"
            );
        }

        return $scope;
    }

    /**
     * Колонки списка: видимые поля и подписи внешних ключей
     *
     * @return string[]
     */
    protected function getListColumns(): array
    {
        $columns = [];
        foreach ($this->fks as $field) {
            $label = $field->fk->table . $field->name . '.' . $field->fk->label . ' ' . $field->name . '_label';
            if ($field->fk->table == $this->table) {
                $label = '(SELECT ' . $field->fk->label . ' FROM `' . $field->fk->table . '` t_' . $this->table
                    . ' WHERE ' . $this->pk->name . ' = ' . $this->table . '.' . $field->name . ') ' . $field->name . '_label';
            }
            $columns[] = $this->table . '.' . $field->name;
            $columns[] = $label;
        }
        foreach ($this->fields as $field) {
            if ($field->isVisible && !isset($this->fks[$field->name]) && ($column = $field->selectQueryField())) {
                $columns[] = $column;
            }
        }

        return array_merge($columns, $this->selectAdditional);
    }

    /**
     * Запрос списка без фильтров, сортировки и страниц: колонки, таблицы внешних ключей
     * и записи, которые пользователь может видеть (getScope())
     */
    protected function getListQuery(): AQ
    {
        $query = new AQ()->asArray()->from($this->table)->select($this->selectAll ? '*' : $this->getListColumns());
        foreach ($this->fks as $field) {
            if ($field->fk->table != $this->table) {
                // алиас: на одну таблицу могут ссылаться несколько полей
                $alias = $field->fk->table . $field->name;
                $query->join(
                    $field->fk->table,
                    $this->table . '.' . $field->name,
                    $alias . '.' . $field->fk->key,
                    $field->isnull ? 'LEFT' : 'INNER',
                    alias: $alias,
                );
            }
        }

        return $query->where($this->getScope());
    }

    protected function getListOrder(): string
    {
        $column = $this->getSortColumn();
        $order = [$column . ($this->isSortDesc() ? ' DESC' : '')];
        if ($this->pk->name !== $column) {
            $order[] = $this->pk->name;
        }

        return implode(', ', $order);
    }

    /**
     * Сортировка, выбранная кликом по заголовку колонки: ?o=column, повторный клик меняет порядок на обратный.
     * Хранится в сессии
     */
    protected function changeSorting(): void
    {
        $column = (string)($_REQUEST['o'] ?? '');
        if (!isset($this->fields[$column])) {
            return;
        }

        $sorting = &$_SESSION[$this->table];
        $sorting['order_desc'] = ($sorting['order_by'] ?? '') === $column ? (int)!($sorting['order_desc'] ?? 0) : 0;
        $sorting['order_by'] = $column;
    }

    /**
     * Колонка сортировки списка: выбранная, по умолчанию $defaultOrderBy или первичный ключ
     */
    public function getSortColumn(): string
    {
        $column = (string)($_SESSION[$this->table]['order_by'] ?? '');
        return isset($this->fields[$column]) ? $column : (static::$defaultOrderBy ?? $this->pk->name);
    }

    public function isSortDesc(): bool
    {
        return isset($this->fields[(string)($_SESSION[$this->table]['order_by'] ?? '')])
            ? (bool)($_SESSION[$this->table]['order_desc'] ?? false)
            : static::$defaultOrderDesc;
    }

    protected function getListCount(AQ $query): int
    {
        return (int)(clone $query)->select('COUNT(*)')->fetchScalar();
    }

    protected function filterReset(): never
    {
        $_SESSION[$this->table] = [];
        header('location: ' . Core::path());
        exit;
    }

    /**
     * Добавляет в запрос фильтры списка (выбранные в списке, хранятся в сессии), значения передаются
     * параметрами запроса
     */
    protected function applyFilters(AQ $query): void
    {
        foreach ($this->fields as $field) {
            $filter = &$_SESSION[$this->table]['filter'][$field->name];
            $filter = $_REQUEST['filter'][$field->name] ?? $filter ?? '';
            if ($filter === '') {
                continue;
            }

            $column = $this->table . '.' . $field->name;
            $bind = 'filter_' . $field->name;
            if ($filter === 'null') {
                $query->andWhere([$column => null]);
                continue;
            }

            $this->isHierarchy = false;
            if ($field instanceof FieldDateTime) {
                $query->andWhere($this->wrapColumn($column) . " LIKE :$bind")->bind([$bind => Time::mysql($filter) . '%']);
            } elseif ($field instanceof FieldMultiKey) {
                $relations = DB::wrapName($field->params['table_relations']);
                $query->andWhere(
                    "EXISTS (SELECT 1 FROM $relations rls WHERE " . $this->wrapColumn($this->table . '.' . $this->pk->name)
                    . ' = rls.' . DB::wrapName($field->getRelationKey())
                    . ' AND rls.' . DB::wrapName($field->params['key']) . " = :$bind)"
                )->bind([$bind => $filter]);
            } elseif ($field instanceof FieldString) {
                $query->andWhere($this->wrapColumn($column) . " LIKE :$bind")->bind([$bind => "%$filter%"]);
            } elseif (str_contains($filter, '%')) {
                $query->andWhere($this->wrapColumn($column) . " LIKE :$bind")->bind([$bind => $filter]);
            } else {
                $query->andWhere([$column => $filter]);
            }
        }
        unset($filter);
    }

    public function show(): string
    {
        $this->changeSorting();
        $query = $this->getListQuery();
        $this->applyFilters($query);

        $cnt = $this->getListCount($query);
        if ($cnt > 200 && $this->isHierarchy) {
            $this->isHierarchy = false;
            if ($_SESSION[$this->table]['filter'][$this->pid->name] !== '') {
                $query->andWhere([$this->table . '.' . $this->pid->name => $_SESSION[$this->table]['filter'][$this->pid->name]]);
                $cnt = $this->getListCount($query);
            }
        }

        $pagination = new Pagination((int)($_GET['p'] ?? 0), $this->isHierarchy ? 200 : static::$perPage, $cnt);
        $query->orderBy($this->getListOrder());
        if (!$this->isHierarchy) {
            $query->limit($pagination->limit());
        }
        self::$currentQuery = $query;
        $listRows = $query->all();
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
            foreach ($listRows as $row) {
                $tree[(int)$row[$this->pid->name]][(int)$row[$this->pk->name]] = $row;
            }
            $id_start = isset($_SESSION[$this->table]['filter'][$this->pid->name]) ? (int)$_SESSION[$this->table]['filter'][$this->pid->name] : 0;
            $list = Tree::toList($tree, $id_start);
            foreach ($list as $l) {
                if ($l['tree_level']) {
                    $l[$tree_name] = '<div style="padding-left:' . (25 * $l['tree_level']) . 'px">' . $l[$tree_name] . '</div>';
                }
                $rows[] = $l;
            }
        } else {
            $rows = $listRows;
        }

        $saveId = (int)@$_GET['save_id'];

        return $this->render(__DIR__ . '/tpl/show.tpl', get_defined_vars());
    }

    protected function showCell(Field $field, array $row): string
    {
        return $field->show($row);
    }

    protected function initTable(): void
    {
        $fields = static::getFieldRows();
        $pkName = '';
        foreach ($fields as $field) {
            if (!empty($field['params']['main']['filter'])) {
                $this->isFilter = true;
            }
            if (!empty($field['params']['main']['pk'])) {
                $pkName = $field['name'];
            }

            $this->addField(Helper::create($field));
        }

        foreach ($this->fields as $field) {
            $field->tablePk = $pkName;
        }

        //ПАРАМЕТРЫ ТАБЛИЦЫ
        $this->tableParamsInit();
    }

    /**
     * Загрузка параметров из БД
     */
    protected function tableParamsLoad(): array
    {
        return static::getParamRows();
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

    protected function filter(): string
    {
        return $this->isFilter ? $this->render(__DIR__ . '/tpl/filter.tpl') : '';
    }

    protected function filterField(Field $field): string
    {
        return $field->filter($_REQUEST['filter'][$field->name] ?? '');
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->name] = $field;
        if ($field->pk) {
            $this->pk = $field;
        }
        if ($field->fk) {
            $this->fks[$field->name] = $field;
            if ($field->fk->table == $this->table) {
                $this->pid = $field;
                $this->isHierarchy = true;
            }
        }
    }

    /**
     * Переключает булево поле, возвращает новое значение ('1' / '0') или 'error'
     */
    public function boolChange(): string
    {
        $pk = (int)$_REQUEST['pk'];
        $field = $_REQUEST['field'];
        if ($pk && isset($this->fields[$field])) {
            $fieldDB = static::getFieldRow($field);
            $fieldParams = $fieldDB['params'] ?? [];
            $where = [$this->pk->name => $pk];
            $value = $this->getQuery()->select(DB::wrapName($field))->where($where)->fetchScalar();
            if ($value === null) {
                return 'error';
            }
            if (!@$fieldParams['main']['readonly']) {
                $value = $value ? 0 : 1;
                static::getModel()::bulkUpdate([$field => $value], $where);
            }
            return $value ? '1' : '0';
        }
        return 'error';
    }

    public function form(): string
    {
        if (isset($_GET['ids']) && count(explode(',', $_GET['ids'])) == 1) {
            header("location: ?action=form&{$this->pk->name}={$_GET['ids']}");
            exit;
        }

        $where = $this->getScope();

        $title = 'Добавить запись';

        $isGroup = !empty($_GET['ids']);
        $isAdd = !$isGroup && empty($_REQUEST[$this->pk->name]);
        $isEdit = !$isGroup && !$isAdd;

        if ($isAdd && !$this->canAdd) {
            Alert::error('Ошибка! Недостаточно прав для добавления записей в данный раздел', Core::path());
        }
        if ($isEdit && !$this->canEdit) {
            Alert::error('Ошибка! Недостаточно прав для редактирования записей в данном разделе', Core::path());
        }

        $row = [];
        foreach ($this->fields as $index => $field) {
            if ($field instanceof FieldVirtual) {
                unset($this->fields[$index]);
            }
        }
        $ui = '';
        foreach ($this->fields as $field) {
            $row[$field->name] = $field->defval();
            $ui .= $field->loadUI(true);
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
            $where[$this->pk->name] = $pk;
            if ($rrow = $this->getQuery()->where($where)->fetchOne()) {
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
                Alert::error('Ошибка! Запись не найдена или недостаточно прав для редактирования.', Core::path());
            }
        }

        if ($isGroup) {
            $title = 'Редактирование нескольких записей';
            $ids = DB::escape($_GET['ids']);
            $rows = $this->getQuery()->where([$this->pk->name => array_map('intval', explode(',', (string)$ids))])->all();
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

        if (Core::$isAjax) {
            if (!empty($_REQUEST['field']) && isset($this->fields[$_REQUEST['field']]) && $pk) {
                $field = $this->fields[$_REQUEST['field']];
                static::getModel()::bulkUpdate([$field->name => ''], $where);
                $field->delete($row[$field->name]);
                exit();
            }
        }

        $params = [];
        if (!empty($row['params'])) {
            $params = unserialize($row['params']);
        }

        $this->formBeforeOutput($row);
        return $ui . $this->render(__DIR__ . '/tpl/form.tpl', get_defined_vars());
    }

    protected function formBeforeOutput(array &$row): void
    {
    }

    /**
     * Вывод html-кода для ввода данных на форме. Пустая (NULL) колонка и поле без колонки выводятся пустой строкой
     */
    protected function formFieldInput(Field $field, array $row): string
    {
        return $field->input($field->value ?: ($row[$field->name] ?? ''));
    }

    public function showActions(): string
    {
        return $this->render(__DIR__ . '/tpl/show.actions.tpl');
    }

    public function rowActions($row, $i): string
    {
        return $this->render(__DIR__ . '/tpl/show.actions.row.tpl', ['row' => $row, 'i' => $i]);
    }

    /**
     * Ajax: поиск записей для выпадающего списка внешнего ключа (FieldInt с is_fk), JSON [{id, name}]
     */
    public function searchInt(): array
    {
        $text = trim((string)($_REQUEST['text'] ?? ''));
        $name = str_replace(['filter[', ']'], '', (string)($_REQUEST['name'] ?? ''));
        $field = $this->fields[$name] ?? null;
        if (!($field instanceof FieldInt) || empty($field->params['is_fk'])) {
            return [];
        }

        $label = DB::wrapName($field->params['fk_label']);
        $out = (new AQ())->asArray()->from($field->params['fk_table'])
            ->select([DB::wrapName($field->params['fk_key']) . ' AS id', "$label AS name"])
            ->where("$label LIKE :text")
            ->bind(['text' => '%' . $text . '%'])
            ->limit(50)
            ->all();

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Ajax: поиск записей для фильтра по полю FieldMultiKey, JSON [{id, name}]
     */
    public function searchMultiKey(): array
    {
        $name = str_replace(['filter[', ']'], '', (string)($_REQUEST['name'] ?? ''));
        $field = $this->fields[$name] ?? null;
        if (!($field instanceof FieldMultiKey)) {
            return [];
        }

        $out = [];
        foreach ($field->getValues(search: (string)($_REQUEST['text'] ?? ''), limit: 50) as $id => $label) {
            $out[] = ['id' => $id, 'name' => $label];
        }

        exit(json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    public function copy(): never
    {
        $ids = array_map('intval', explode(',', $this->ids));
        $rows = $this->getQuery()->where([$this->pk->name => $ids])->all();
        $fieldNames = ['name', 'title', 'label'];
        foreach ($rows as $row) {
            $set = [];
            foreach ($row as $key => $value) {
                if ($key == $this->pk->name || $value === null) {
                    continue;
                }
                $set[$key] = in_array($key, $fieldNames) ? "Копия $value" : $value;
            }
            $copy = static::getModel()::insertStatic($set);
            if ($copy) {
                Alert::success("Запись №{$row[$this->pk->name]} скопирована в №{$copy->getId()}");
            } else {
                Alert::error("Запись №{$row[$this->pk->name]} не скопирована!");
                Alert::error(DB::error());
                Alert::error(DB::error());
            }
        }
        header('location: ' . Core::path());
        exit;
    }

    /**
     * Ajax-действие: проверяет отправленную форму, возвращает JSON {valid, errors}
     */
    public function validate(): string
    {
        $json = ['valid' => $this->isValid() ? 1 : 0];
        foreach ($this->errors as $field => $errors) {
            $json['errors'][$field] = join('<br />', $errors);
        }
        return json_encode($json);
    }

    public function isValid(): bool
    {
        foreach ($this->fields as $field) {
            if ($errors = $field->check()) {
                foreach ($errors as $error) {
                    $this->errors[$field->label][] = $error;
                }
            }
        }
        return !count($this->errors);
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
        $pk = $this->pk->name;

        $set = [];
        foreach ($this->fields as $field) {
            if (isset($_POST['set'][$field->name]) && !($field instanceof FieldVirtual)) {
                $set[$field->name] = $field->getPOST();
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
            $rows = $this->getQuery()->select([DB::wrapName($pk), 'params'])->where([$pk => $idList])->all();
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

                $success = static::getModel()::bulkUpdate(['params' => serialize($params)], [$pk => $row[$pk]]) && $success;
            }
        }

        if ($set) {
            $success = static::getModel()::bulkUpdate($set, [$pk => $idList]) && $success;
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

        $pkValue = null;
        if ($this->isValid()) {
            foreach ($this->fields as $index => $field) {
                if ($field instanceof FieldVirtual) {
                    unset($this->fields[$index]);
                }
            }

            $isInsert = empty($_POST[$this->pk->name]);
            $isUpdate = !$isInsert;
            // через модель: срабатывают её хуки (beforeSave() и др.) и события
            if ($isInsert) {
                $model = static::getModel()::insertStatic($this->getInsertData());
                $saved = $model !== null;
            } else {
                $model = static::getModel()::findOne([$this->pk->name => (int)$_POST[$this->pk->name]]);
                $saved = $model && $model->update($this->getUpdateData());
            }

            if (!$saved || DB::errno()) {
                $this->errors[] = 'Введенные данные некорректны';
                $this->errors[] = DB::error() ?: 'Запись не найдена';
            } else {
                if ($isInsert) {
                    $_POST[$this->pk->name] = $_REQUEST[$this->pk->name] = $model->getId();
                    // поля, которые пишут данные по id записи (связи), до вставки ничего не сохранили
                    foreach ($this->fields as $field) {
                        if ($field->isSavedById) {
                            $field->getPOST();
                        }
                    }
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
                    $pk = $this->table . '.' . $this->pk->name;
                    $where = $this->getScope();
                    $where[$pk] = (int)$_POST[$this->pk->name];
                    if ($this->pid) {
                        // путь записи по пути родителя, затем пути её потомков
                        $pid = $this->table . '.' . $this->pid->name;
                        $row = $this->getQuery()
                            ->select([$pk, $pid, $this->table . '.' . $field_alias->name, 't2.' . $field_path->name])
                            ->leftJoin($this->table, $pid, 't2.' . $this->pk->name, alias: 't2')
                            ->where($where)
                            ->fetchOne();
                        $path = $row['path'] ? $row['path'] . $row['alias'] . '/' : '/' . $row['alias'] . '/';
                        $path = preg_replace('@\/+@', '/', $path);
                        $row[$field_path->name] = $path;
                        static::getModel()::bulkUpdate([$field_path->name => $path], $where);

                        $steck = [$row];
                        for ($i = 0; $i < count($steck); $i++) {
                            $cur = $steck[$i];
                            $children = $this->getQuery()
                                ->select([$this->pk->name, $field_alias->name, $field_path->name])
                                ->where(array_merge($this->getScope(), [$pid => $cur[$this->pk->name]]))
                                ->all();
                            foreach ($children as $child) {
                                $path = $cur[$field_path->name] . $child[$field_alias->name] . '/';
                                if ($child[$field_path->name] != $path) {
                                    static::getModel()::bulkUpdate(
                                        [$field_path->name => $path],
                                        array_merge($this->getScope(), [$pk => $child[$this->pk->name]])
                                    );
                                    $child[$field_path->name] = $path;
                                    $steck[] = $child;
                                }
                            }
                        }
                    } else {
                        static::getModel()::bulkUpdate([$field_path->name => '/' . $_POST[$field_alias->name] . '/'], $where);
                    }
                }
            }
            if (count($this->errors)) {
                Alert::error('Ошибка при сохранении записи' . ($pkValue ? " №$pkValue" : ''));
            } else {
                Alert::success('Запись №' . $pkValue . ' успешно сохранена!');
            }
        }

        // при ошибках content() снова показывает форму с ошибками и отправленными значениями
        return count($this->errors) ? false : $pkValue;
    }

    /**
     * Значения новой записи из формы: колонка => значение. Пустые значения колонок со значением по умолчанию
     * в БД не передаются — запись получит значение по умолчанию
     */
    protected function getInsertData(): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            }
            $add = !$field->isVirtual && !$field->readonly || $field instanceof FieldPath;
            $post = $field->getPOST();
            if ($add && (empty($post) || $post == 'NULL' || $post == "''")) {
                $add = empty(DB::columnInfo($this->table, $field->name)['Default']);
            }
            if ($add) {
                $data[$field->name] = $field->name == 'params' ? serialize($this->getPostedParams()) : $post;
            }
        }

        return $data;
    }

    /**
     * Изменённые значения записи из формы: колонка => значение. Пустой пароль, пустой файл и пустая дата
     * не меняют сохранённое значение
     */
    protected function getUpdateData(): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            if ($field->isVirtual) {
                $field->getPOST();
            } elseif (!$field->readonly) {
                if ($field->name == 'params') {
                    $data[$field->name] = serialize($this->getPostedParams());
                } elseif ($field instanceof FieldPassword) {
                    if ($value = $field->getPOST()) {
                        $data[$field->name] = $value;
                    }
                } elseif ($field instanceof FieldDateTime) {
                    // колонку с автозаполнением (ON UPDATE) не трогаем
                    if (empty(DB::columnInfo($this->table, $field->name)['Extra'])) {
                        $value = $field->getPOST();
                        // null — очистка поля (e2n), пустая строка — оставить как есть
                        if ($value !== '') {
                            $data[$field->name] = $value;
                        }
                    }
                } elseif ($field instanceof FieldFile) {
                    if ($value = trim($field->getPOST(), "'")) {
                        $data[$field->name] = $value;
                    }
                } else {
                    $data[$field->name] = $field->getPOST();
                }
            }
        }

        return $data;
    }

    protected function getPostedParams(): array
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
        $where = $this->getScope();
        $where[$this->pk->name] = $id;
        static::getModel()::bulkDelete($where);
        return DB::errno() ? DB::errno() . ': ' . DB::error() : '';
    }

    /**
     * Обработчик кнопки "удалить"
     */
    public function delete(): never
    {
        if (!$this->canDelete) {
            Alert::error('Ошибка! Недостаточно прав для удаления записей в данном разделе', Core::path());
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
                $rows = $ids ? $this->getQuery()->select(DB::wrapName($field->name))->where([$this->pk->name => $ids])->all() : [];
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
        header('location: ' . Core::path());
        exit;
    }

    protected function portlets(string $position = 'right'): string
    {
        return '';
    }

    protected function showDetailExtra($row): string
    {
        return '';
    }

    public function showDetail(): string
    {
        $id = (int)@$_GET['id'];
        return $this->render($this->showDetailGetTpl(), ['row' => $this->showDetailGetRow($id)]);
    }

    /**
     * Абсолютный путь к шаблону карточки записи
     */
    protected function showDetailGetTpl(): string
    {
        return __DIR__ . '/tpl/show.detail.tpl';
    }

    protected function showDetailGetRow(int $id): array|false|null
    {
        return $this->getListQuery()->andWhere([$this->table . '.' . $this->pk->name => $id])->fetchOne();
    }

    /**
     * @throws \Exception code == 999 — пропустить запись
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

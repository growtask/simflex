<?php

namespace Simflex\Core;

use ArrayAccess;
use Exception;
use JsonSerializable;
use ReflectionClass;
use Simflex\DB\AQ;
use Simflex\DB\Expr;
use Simflex\DB\Where;
use Simflex\Core\Events\Event;
use Simflex\Core\Events\Events;
use Simflex\Helpers\Str;
use Simflex\Models\Attrib\FieldMany;
use Simflex\Models\Attrib\FieldOne;
use Simflex\Models\Enums\Relation;
use Simflex\DB\DB;

/**
 * Класс ModelBase
 *
 * Базовый класс любой ORM-модели
 * В дочернем классе ОБЯЗАТЕЛЬНО задать статические свойства $table и $primaryKeyName
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable
{
    public const FLAG_IGNORE = 1 << 0;
    public const FLAG_SKIP_VIRTUAL = 1 << 1;

    /**
     * @var array Для отладки. Считает вставки, удаления и обновления.
     */
    public static array $stats = [
        'inserted' => 0,
        'updated' => 0,
        'deleted' => 0,
    ];

    /**
     * @var int|null ID модели
     */
    protected ?int $id = null;

    /**
     * @var array Данные модели
     */
    protected array $data = [];

    /**
     * @var array Данные модели до обновления
     */
    protected array $dataBeforeUpdate = [];

    /**
     * @var array Кэш связей
     */
    protected array $relations = [];

    /**
     * @var string Имя таблицы
     */
    protected static string $table;

    /**
     * @var string Имя первичного ключа
     */
    protected static string $primaryKeyName;

    /**
     * @var array{code: int, text: string} Последняя ошибка
     */
    protected array $lastError = ['code' => 0, 'text' => ''];

    /**
     * Имя таблицы
     *
     * @param bool $wrap Обернуть имя таблицы в обратные кавычки
     * @return string Имя таблицы
     */
    public static function getTableName(bool $wrap = false): string
    {
        return $wrap ? DB::wrapName(static::$table) : static::$table;
    }

    /**
     * Имя первичного ключа
     *
     * @param bool $wrap Обернуть имя первичного ключа в обратные кавычки
     * @return string Имя первичного ключа
     */
    public static function getPrimaryKeyName(bool $wrap = false): string
    {
        return $wrap ? DB::wrapName(static::$primaryKeyName) : static::$primaryKeyName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Конструктор ModelBase.
     * @param int|null $id ID модели
     * @throws Exception
     */
    public function __construct(?int $id = null)
    {
        $this->loadRelations();
        if ($id) {
            $this->load($id);
        }
    }

    /**
     * Загружает связи из рефлексии в локальный кэш для скорости
     */
    protected function loadRelations(): void
    {
        $class = new ReflectionClass($this);
        foreach ($class->getAttributes() as $attribute) {
            if ($attribute->getName() === FieldOne::class) {
                /** @var FieldOne $attrib */
                $attrib = $attribute->newInstance();
                $this->relations[$attrib->name] = ['type' => Relation::One, 'model' => $attrib->model];
            } elseif ($attribute->getName() === FieldMany::class) {
                /** @var FieldMany $attrib */
                $attrib = $attribute->newInstance();
                $this->relations[$attrib->name] = ['type' => Relation::Many, 'model' => $attrib->model];
            }
        }
    }

    /**
     * Загружает данные модели
     * @param int $id ID модели
     * @return bool True, если запись найдена и загружена
     * @throws Exception
     */
    public function load(int $id): bool
    {
        if ($row = (new AQ())->from(static::$table)->where([static::$primaryKeyName => $id])->asArray()->fetchOne()) {
            $this->fill($row);
            return true;
        }

        return false;
    }

    /**
     * Перезагружает данные модели
     * @return bool True, если запись найдена и загружена
     * @throws Exception
     */
    public function reload(): bool
    {
        return $this->id && $this->load($this->id);
    }

    /**
     * Значения enum для поля
     */
    public static function enumValues(string $fieldName, bool $withBuffer = true): array
    {
        $closure = fn() => DB::enumValues(static::$table, $fieldName);
        if ($withBuffer) {
            return Buffer::getOrSet('enumValues.' . static::$table . ".$fieldName", $closure);
        }

        return $closure();
    }

    /**
     * Ищет модели по условию
     *
     * @param string|array|Where $where Условие
     * @param string|null $orderBy Выражение ORDER BY
     * @param string|null $limit Выражение LIMIT
     * @param bool|string $assocKey Вернуть массив с ID моделей (или другим полем) в ключах
     * @return static[] Массив моделей
     * @throws Exception
     */
    public static function find(
        string|array|Where $where,
        ?string $orderBy = null,
        ?string $limit = null,
        bool|string $assocKey = false
    ): array {
        return static::findAdv()->andWhere($where)->orderBy($orderBy)->limit($limit ?? '')->all($assocKey);
    }

    /**
     * Подготовленный запрос AQ на основе этой модели
     *
     * @return AQ Запрос AQ
     */
    public static function findAdv(): AQ
    {
        return (new AQ())->from(static::$table)->setModelClass(static::class);
    }

    /**
     * Проверяет, существует ли запись
     *
     * @param int $id ID модели
     * @return bool True, если найдена
     * @throws Exception
     */
    public static function exists(int $id): bool
    {
        return !!static::findAdv()->select('count(*)')->where([static::$primaryKeyName => $id])->fetchScalar();
    }

    /**
     * Ищет одну модель по условию
     *
     * @param string|array|Where $where Условие
     * @param bool $returnModelIfNotFound Вернуть новую модель, если не найдена
     * @return static|null Модель или null, если не найдена
     * @throws Exception
     */
    public static function findOne(string|array|Where $where, bool $returnModelIfNotFound = false): ?static
    {
        $row = static::findAdv()->where($where)->limit(1)->fetchOne();
        return $row ?? ($returnModelIfNotFound ? new static() : null);
    }

    /**
     * Все модели
     *
     * @return static[]
     * @throws Exception
     */
    public static function all(): array
    {
        return self::findAdv()->all();
    }

    /**
     * Заполняет модель данными
     */
    public function fill(array $data): static
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelFill, static::class, $this));

        $this->data = array_merge($this->data, $data);
        $this->id = $this->data[static::$primaryKeyName] ?? null;

        $ev->dispatch(new Event(Events::PostModelFill, static::class, $this));
        $this->afterFill();
        return $this;
    }

    /**
     * Вызывается после заполнения данных модели
     */
    protected function afterFill(): void
    {
    }

    /**
     * Записывает данные модели в базу
     *
     * @param array|null $data Данные взамен текущих
     * @return bool True при успехе
     * @throws Exception
     */
    public function save(?array $data = null): bool
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelSave, static::class, $this));

        $result = false;
        if ($this->beforeSave()) {
            $result = $this->id ? $this->update($data) : $this->insert($data);
        }

        $ev->dispatch(new Event(Events::PostModelSave, static::class, $this, $result));
        $this->afterSave($result);
        return $result;
    }

    /**
     * Вставляет данные модели в базу
     *
     * @param array|null $data Данные взамен текущих
     * @param int $flags Флаги вставки
     */
    public function insert(?array $data = null, int $flags = self::FLAG_SKIP_VIRTUAL): bool
    {
        if ($data) {
            $this->fill($data);
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelInsert, static::class, $this));

        $result = false;
        if ($this->beforeInsert()) {
            $keys = [];
            $values = [];

            foreach ($this->data as $key => $value) {
                // пропускаем виртуальные ключи
                if (($flags & self::FLAG_SKIP_VIRTUAL) && method_exists(static::class, 'offsetGet' . $key)) {
                    continue;
                }

                $keys[] = $key;
                $values[] = is_bool($value) ? ($value ? 1 : 0) : $value;
            }

            // собираем запрос вставки
            $ignore = $flags & self::FLAG_IGNORE ? ' ignore ' : '';
            $query = "insert $ignore into " . static::getTableName(true) . ' (';
            $query .= implode(', ', array_map(fn($key) => DB::wrapName($key), $keys)) . ') values (';
            $query .= implode(
                ', ',
                array_map(fn($value) => $value instanceof Expr ? (string)$value : '?', $values)
            ) . ')';

            // убираем выражения Expr из параметров запроса
            $bind = [];
            foreach ($values as $value) {
                if (!($value instanceof Expr)) {
                    $bind[] = $value;
                }
            }

            if ($result = $this->query($query, $bind)) {
                $this->id = $this->{static::$primaryKeyName} = DB::insertId();
            }
        }

        $ev->dispatch(new Event(Events::PostModelInsert, static::class, $this, $result));
        $this->afterInsert($result);
        return $result;
    }

    /**
     * Вставляет данные модели в базу и возвращает экземпляр модели
     *
     * @param array $data Начальные данные
     * @param int $flags Флаги вставки
     * @return static|null Экземпляр модели или null при ошибке
     * @throws Exception
     */
    public static function insertStatic(array $data, int $flags = self::FLAG_SKIP_VIRTUAL): ?static
    {
        $model = new static();
        if ($model->insert($data, $flags)) {
            $model->reload();
            return $model;
        }

        return null;
    }

    /**
     * Вставляет сразу много моделей
     *
     * @param array $items Массив массивов с данными моделей
     * @param int $flags Флаги вставки
     * @return bool True, если все модели успешно вставлены
     */
    public static function bulkInsert(array $items, int $flags = self::FLAG_SKIP_VIRTUAL): bool
    {
        $success = true;
        foreach ($items as $item) {
            $model = new static();
            $success &= $model->insert($item, $flags);
        }

        return $success;
    }

    /**
     * Обновляет данные модели в базе
     *
     * @param array|null $data Данные взамен текущих
     * @return bool True при успехе
     * @throws Exception
     */
    public function update(?array $data = null, int $flags = self::FLAG_SKIP_VIRTUAL): bool
    {
        if (!$this->id) {
            return false;
        }

        if ($data) {
            $this->fill($data);
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelUpdate, static::class, $this));

        $result = false;
        if ($this->beforeUpdate()) {
            $updateData = $this->data;
            unset($updateData[static::$primaryKeyName]);

            foreach ($this->data as $key => $value) {
                // пропускаем виртуальные ключи
                if (!($flags & self::FLAG_SKIP_VIRTUAL) || !method_exists(static::class, 'offsetGet' . $key)) {
                    continue;
                }

                unset($updateData[$key]);
            }

            $query = 'update ' . static::getTableName(true) . ' set ';
            $query .= implode(', ', array_map(fn($key) => DB::wrapName($key) . ' = ?', array_keys($updateData)));
            $query .= ' where ' . static::getPrimaryKeyName(true) . ' = ?';

            if ($result = $this->query($query, array_merge(array_values($updateData), [$this->id]))) {
                $this->reload();
            }
        }

        $ev->dispatch(new Event(Events::PostModelUpdate, static::class, $this, $result));
        $this->afterUpdate($result);
        return $result;
    }

    /**
     * Удаляет модель из базы
     */
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelDelete, static::class, $this));

        $result = false;
        $oldData = $this->toArray();

        if ($this->beforeDelete()) {
            $query = 'delete from ' . static::getTableName(true) . ' where ' . static::getPrimaryKeyName(true) . ' = ?';
            $result = $this->query($query, [$this->id]);
            $this->fill([]);
        }

        $ev->dispatch(new Event(Events::PostModelDelete, static::class, $oldData, $result));
        $this->afterDelete($result, $oldData);
        return $result;
    }

    /**
     * @throws Exception
     */
    public static function bulkDelete(string|array|Where $where, bool $viaModels = false): bool
    {
        if ($viaModels) {
            $success = true;
            foreach (static::find($where) as $model) {
                $success &= $model->delete();
            }

            return $success;
        }

        $where = new Where($where);
        $query = 'delete from ' . static::getTableName(true) . ' ' . $where; // параметры заполняет приведение к строке

        return !!DB::query($query, $where->getBinds());
    }

    /**
     * Обновляет сразу много моделей
     *
     * @param array $set Массив массивов с данными моделей
     * @param string|array|Where $where Условие
     * @param bool $viaModels Обновлять модели по одной
     * @return bool True, если все модели успешно обновлены
     * @throws Exception
     */
    public static function bulkUpdate(array $set, string|array|Where $where, bool $viaModels = false): bool
    {
        if ($viaModels) {
            $success = true;
            foreach (static::find($where) as $model) {
                $success &= $model->update($set);
            }

            return $success;
        } else {
            // только именованные параметры: Where создаёт именованные, а PDO не смешивает их с позиционными
            $bind = [];
            $assign = [];
            foreach (array_values($set) as $i => $value) {
                $assign[] = DB::wrapName(array_keys($set)[$i]) . " = :set_$i";
                $bind["set_$i"] = $value;
            }

            $where = new Where($where);
            $query = 'update ' . static::getTableName(true) . ' set ' . implode(', ', $assign);
            $query .= ' ' . $where; // параметры заполняет приведение к строке

            return !!DB::query($query, array_merge($bind, $where->getBinds()));
        }
    }

    /**
     * Очищает таблицу
     *
     * @return bool True при успехе
     */
    public static function truncate(): bool
    {
        return !!DB::query('truncate ' . static::getTableName(true));
    }

    /**
     * Выполняет запрос
     *
     * @param string $q Запрос
     * @param array $params Параметры запроса
     * @return bool True при успехе
     */
    protected function query(string $q, array $params = []): bool
    {
        $success = !!DB::query($q, $params);
        if (!$success) {
            $this->lastError['code'] = DB::errno();
            $this->lastError['text'] = DB::error();
        }

        return $success;
    }

    /**
     * Задаёт ошибку
     *
     * @param string|array $text Текст ошибки или массив с ключами 'text' и 'code'
     * @param int|null $code Код ошибки
     */
    protected function setError(
        string|array $text,
        ?int $code = null
    ): void {
        if (is_array($text)) {
            $code = $text['code'] ?? 0;
            $text = $text['text'] ?? '';
        }

        $this->lastError['code'] = $code;
        $this->lastError['text'] = $text;
    }

    /**
     * Последняя ошибка
     *
     * @return array{code: int, text: string} Последняя ошибка
     */
    public function getLastError(): array
    {
        return $this->lastError;
    }

    /**
     * Вызывается перед вставкой
     *
     * @return bool True, если вставку нужно продолжить
     */
    protected function beforeInsert(): bool
    {
        return true;
    }

    /**
     * Вызывается после вставки
     *
     * @param bool $success Результат вставки
     */
    protected function afterInsert(bool $success): void
    {
        if ($success) {
            ++self::$stats['inserted'];
        }
    }

    /**
     * Вызывается перед обновлением
     *
     * @return bool True, если обновление нужно продолжить
     */
    protected function beforeUpdate(): bool
    {
        $this->dataBeforeUpdate = $this->data;
        return true;
    }

    /**
     * Вызывается после обновления
     *
     * @param bool $success Результат обновления
     */
    protected function afterUpdate(bool $success): void
    {
        if ($success) {
            ++self::$stats['updated'];
        }
    }

    /**
     * Вызывается перед удалением
     *
     * @return bool True, если удаление нужно продолжить
     */
    protected function beforeDelete(): bool
    {
        return true;
    }

    /**
     * Вызывается после удаления
     *
     * @param bool $success Результат удаления
     * @param array $oldData Прежние данные модели
     */
    protected function afterDelete(bool $success, array $oldData): void
    {
        if ($success) {
            ++self::$stats['deleted'];
        }
    }

    /**
     * Вызывается перед сохранением
     *
     * @return bool True, если сохранение нужно продолжить
     */
    protected function beforeSave(): bool
    {
        return true;
    }

    /**
     * Вызывается после сохранения
     *
     * @param bool $success Результат сохранения
     */
    protected function afterSave(bool $success): void
    {
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
            if ($offset === static::$primaryKeyName) {
                $this->id = (int)$value;
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
        if ($offset === static::$primaryKeyName) {
            $this->id = null;
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->id && !isset($this->data[$offset])) {
            if ($maybe = ($this->data[Str::toUnderscore($offset)] ?? null)) {
                return $maybe;
            }

            // если это автоматическая связь, загружаем здесь
            if (isset($this->relations[$offset])) {
                $rel = $this->relations[$offset];
                $relClass = $rel['model'];
                $relKey = $relClass::$primaryKeyName;

                $this->data[$offset] = $relClass::{$rel['type'] === Relation::One ? 'findOne' : 'find'}(
                    [$relKey => $this->{$relKey}]
                );
            }

            if (method_exists($this, $method = 'offsetGet' . $offset)) {
                $this->data[$offset] = $this->$method();
            }

            if (method_exists($this, $method = 'offsetGet' . Str::toCamel($offset, false))) {
                $this->data[$offset] = $this->$method();
            }
        }

        return $this->data[$offset] ?? null;
    }

    public function __get(mixed $name): mixed
    {
        return $this->offsetGet($name);
    }

    public function __set(mixed $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function __isset(mixed $name): bool
    {
        return $this->offsetExists($name);
    }

    public function __unset(mixed $name): void
    {
        $this->offsetUnset($name);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @see Core/DB/HowTo/UsingModifiers.md
     */
    public static function aqModifiersDefault(): array
    {
        return [];
    }
}

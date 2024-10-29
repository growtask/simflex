<?php

namespace Simflex\Core;

use ArrayAccess;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Simflex\Core\DB\AQ;
use Simflex\Core\DB\Where;
use Simflex\Core\Events\Event;
use Simflex\Core\Events\Events;
use Simflex\Core\Helpers\Str;

/**
 * Class ModelBase
 *
 * Base class for any ORM model
 * MUST set static properties $table and $primaryKeyName in child class
 */
abstract class ModelBase implements ArrayAccess, JsonSerializable
{
    const FLAG_IGNORE = 1 << 0;
    const FLAG_SKIP_VIRTUAL = 1 << 1;

    /**
     * @var int|null Model ID
     */
    protected ?int $id;

    /**
     * @var array Model data
     */
    protected array $data = [];

    /**
     * @var array Model data before update
     */
    protected array $dataBeforeUpdate = [];

    /**
     * @var string Table name
     */
    protected static $table;

    /**
     * @var string Primary key name
     */
    protected static $primaryKeyName;

    /**
     * @var array Last error
     */
    #[ArrayShape(['code' => 'int', 'text' => 'string'])]
    protected array $lastError = ['code' => 0, 'text' => ''];

    /**
     * Get table name
     *
     * @param bool $wrap Wrap table name in backticks
     * @return string Table name
     */
    public static function getTableName(bool $wrap = false): string
    {
        return $wrap ? DB::wrapName(static::$table) : static::$table;
    }

    /**
     * Get primary key name
     *
     * @param bool $wrap Wrap primary key name in backticks
     * @return string Primary key name
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
     * ModelBase constructor.
     * @param int|null $id Model ID
     * @throws Exception
     */
    public function __construct(?int $id = null)
    {
        if ($id) {
            $this->load($id);
        }
    }

    /**
     * Load model data
     * @param int $id Model ID
     * @return bool True if found and loaded
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
     * Reload model data
     * @return bool True if found and loaded
     * @throws Exception
     */
    public function reload(): bool
    {
        return $this->id && $this->load($this->id);
    }

    /**
     * Get enum values for field
     *
     * @param string $fieldName
     * @param bool $withBuffer
     * @return array
     */
    public static function enumValues(string $fieldName, bool $withBuffer = true): array
    {
        $closure = fn() => DB::enumValues(static::$table, $fieldName);
        if ($withBuffer && class_exists(' Buffer')) {
            return Buffer::getOrSet('enumValues.' . static::$table . ".$fieldName", $closure);
        }

        return $closure();
    }

    /**
     * Find models by where condition
     *
     * @param string|array|Where $where Where condition
     * @param string|null $orderBy ORDER BY clause
     * @param string|null $limit LIMIT clause
     * @param bool|string $assocKey Get result array with model id's (or other field) in keys
     * @return static[] Array of models
     * @throws Exception
     */
    public static function find(
        string|array|Where $where,
        ?string $orderBy = null,
        ?string $limit = null,
        bool|string $assocKey = false
    ): array {
        return static::findAdv()->andWhere($where)->orderBy($orderBy)->limit($limit)->all($assocKey);
    }

    /**
     * Get prepared AQ searcher based on this model
     *
     * @return AQ AQ searcher
     */
    public static function findAdv(): AQ
    {
        return (new AQ())->from(static::$table)->setModelClass(static::class);
    }

    /**
     * Test if row exists
     *
     * @param int $id Model ID
     * @return bool True if found
     * @throws Exception
     */
    public static function exists(int $id): bool
    {
        return !!static::findAdv()->select('count(*)')->where([static::$primaryKeyName => $id])->fetchScalar();
    }

    /**
     * Find one model by where condition
     *
     * @param string|array|Where $where Where condition
     * @param bool $returnModelIfNotFound Return new model if not found
     * @return static|null Model or null if not found
     * @throws Exception
     */
    public static function findOne(string|array|Where $where, bool $returnModelIfNotFound = false): ?static
    {
        $row = static::findAdv()->andWhere($where)->limit(1)->fetchOne();
        return $row ?? ($returnModelIfNotFound ? new static : null);
    }

    /**
     * Get all models
     *
     * @return static[]
     * @throws Exception
     */
    public static function all(): array
    {
        return self::findAdv()->all();
    }

    /**
     * Fill model with data
     *
     * @param array $data
     * @return static
     */
    public function fill(array $data): static
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreModelFill, static::class, $this));

        $this->data = array_merge($data, $this->data);
        $this->id = $this->data[static::$primaryKeyName] ?? null;

        $ev->dispatch(new Event(Events::PostModelFill, static::class, $this));
        $this->afterFill();
        return $this;
    }

    /**
     * Invoked after model data is filled
     * @return void
     */
    protected function afterFill(): void
    {
    }

    /**
     * Write model data to database
     *
     * @param array|null $data Data override
     * @return bool True if success
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
     * Insert model data to database
     *
     * @param array|null $data Data override
     * @param int $flags Insert flags
     * @return bool
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
                // skip virtual keys
                if (($flags & self::FLAG_SKIP_VIRTUAL) && method_exists(static::class, 'offsetGet' . $key)) {
                    continue;
                }

                $keys[] = $key;
                $values[] = $value;
            }

            // build insert query
            $ignore = $flags & self::FLAG_IGNORE ? ' ignore ' : '';
            $query = "insert $ignore into " . static::getTableName(true) . ' (';
            $query .= implode(', ', array_map(fn($key) => DB::wrapName($key), $keys)) . ') values (';
            $query .= implode(', ', array_map(fn($value) => '?', $values)) . ')';

            if ($result = $this->query($query, $values)) {
                $this->id = $this->{static::$primaryKeyName} = DB::insertId();
            }
        }

        $ev->dispatch(new Event(Events::PostModelInsert, static::class, $this, $result));
        $this->afterInsert($result);
        return $result;
    }

    /**
     * Insert model data to database and return model instance
     *
     * @param array $data Initial data
     * @param int $flags Insert flags
     * @return static|null Model instance or null if failed
     * @throws Exception
     */
    public static function insertStatic(array $data, int $flags = self::FLAG_SKIP_VIRTUAL): ?static
    {
        $model = new static;
        if ($model->insert($data, $flags)) {
            $model->reload();
            return $model;
        }

        return null;
    }

    /**
     * Insert many models at once
     *
     * @param array $items Array of arrays with model data
     * @param int $flags Insert flags
     * @return bool True if all models were inserted successfully
     */
    public static function bulkInsert(array $items, int $flags = self::FLAG_SKIP_VIRTUAL): bool
    {
        $success = true;
        foreach ($items as $item) {
            $model = new static;
            $success &= $model->insert($item, $flags);
        }

        return $success;
    }

    /**
     * Update model data in database
     *
     * @param array|null $data Data override
     * @return bool True if success
     * @throws Exception
     */
    public function update(?array $data = null): bool
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
     * Delete model from database
     *
     * @return bool
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
    public static function bulkDelete($where, bool $viaModels = false): bool
    {
        if ($viaModels) {
            $success = true;
            foreach (static::find($where) as $model) {
                $success &= $model->delete();
            }

            return $success;
        }

        return !!DB::query('delete from ' . static::getTableName(true) . ' ' . (new Where($where)));
    }

    /**
     * Update many models at once
     *
     * @param array $set Array of arrays with model data
     * @param string|array|Where $where Where condition
     * @param bool $viaModels Update models one by one
     * @return bool True if all models were updated successfully
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
            $query = 'update ' . static::getTableName(true) . ' set ';
            $query .= implode(', ', array_map(fn($key) => DB::wrapName($key) . ' = ?', array_keys($set)));
            $query .= ' where ' . (new Where($where));

            return !!DB::query($query, array_values($set));
        }
    }

    /**
     * Truncate table
     *
     * @return bool True if success
     */
    public static function truncate(): bool
    {
        return !!DB::query('truncate ' . static::getTableName(true));
    }

    /**
     * Run query
     *
     * @param string $q Query
     * @param array $params Prepared params
     * @return bool True if success
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
     * Set error
     *
     * @param string|array $text Error text or array with 'text' and 'code' keys
     * @param int|null $code Error code
     * @return void
     */
    protected function setError(
        #[ArrayShape(['text' => 'string', 'code' => 'int'])] string|array $text,
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
     * Get last error
     *
     * @return array Last error
     */
    #[ArrayShape(['code' => 'int', 'text' => 'string'])]
    public function getLastError(): array
    {
        return $this->lastError;
    }

    /**
     * Invoked before insert
     *
     * @return bool True if insert should be continued
     */
    protected function beforeInsert(): bool
    {
        return true;
    }

    /**
     * Invoked after insert
     *
     * @param bool $success Insert result
     * @return void
     */
    protected function afterInsert(bool $success): void
    {
    }

    /**
     * Invoked before update
     *
     * @return bool True if update should be continued
     */
    protected function beforeUpdate(): bool
    {
        $this->dataBeforeUpdate = $this->data;
        return true;
    }

    /**
     * Invoked after update
     *
     * @param bool $success Update result
     * @return void
     */
    protected function afterUpdate(bool $success): void
    {
    }

    /**
     * Invoked before delete
     *
     * @return bool True if delete should be continued
     */
    protected function beforeDelete(): bool
    {
        return true;
    }

    /**
     * Invoked after delete
     *
     * @param bool $success Delete result
     * @param array $oldData Old model data
     * @return void
     */
    protected function afterDelete(bool $success, array $oldData): void
    {
    }

    /**
     * Invoked before save
     *
     * @return bool True if save should be continued
     */
    protected function beforeSave(): bool
    {
        return true;
    }

    /**
     * Invoked after save
     *
     * @param bool $success Save result
     * @return void
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
            if ($offset == static::$primaryKeyName) {
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
        if ($offset == static::$primaryKeyName) {
            $this->id = null;
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->id && !isset($this->data[$offset])) {
            if ($maybe = ($this->data[Str::toUnderscore($offset)] ?? null)) {
                return $maybe;
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
     * @return array
     * @see Core/DB/HowTo/UsingModifiers.md
     */
    public static function aqModifiersDefault(): array
    {
        return [];
    }
}

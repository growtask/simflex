<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Fields\Field;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamDefinition;
use Simflex\Admin\Structure\Data\TableDefinition;

class Repository
{
    protected static ?array $tables = null;
    protected static ?array $fieldTypes = null;

    public static function table(string $name): array
    {
        $tables = static::tables();
        return $tables[$name] ?? [];
    }

    public static function fields(string $table): array
    {
        $tableData = static::table($table);
        if (!isset($tableData['fields'])) {
            return [];
        }

        $out = [];
        foreach ($tableData['fields'] as $key => $field) {
            $out[] = static::normalizeField($field, $table, is_string($key) ? $key : null);
        }

        usort($out, fn(array $a, array $b) => [$a['npp'], $a['name']] <=> [$b['npp'], $b['name']]);
        return $out;
    }

    public static function field(string $table, string $name): array
    {
        foreach (static::fields($table) as $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }

        return [];
    }

    public static function params(string $table): array
    {
        $tableData = static::table($table);
        if (!isset($tableData['params'])) {
            return [];
        }

        $out = [];
        foreach ($tableData['params'] as $key => $param) {
            $param = static::normalizeParam($param, $table, is_string($key) ? $key : null);
            $out[(string)$param['param_pid']][(string)$param['param_id']] = $param;
        }

        return $out;
    }

    public static function fieldTypes(): array
    {
        if (static::$fieldTypes !== null) {
            return static::$fieldTypes;
        }

        $types = [];
        foreach (static::discoverFieldTypeClasses() as $class) {
            $types[$class] = [
                'name' => $class::typeLabel(),
                'class' => $class,
                'params' => $class::typeParams(),
            ];
        }

        uasort($types, fn(array $a, array $b) => $a['name'] <=> $b['name']);
        static::$fieldTypes = $types;
        return static::$fieldTypes;
    }

    public static function fieldTypeByClass(string $class): array
    {
        $class = static::normalizeFieldType($class);
        $types = static::fieldTypes();
        return $types[$class] ?? [];
    }

    public static function fieldParamsByType(string $fieldType): array
    {
        $type = static::fieldTypeByClass($fieldType);
        $params = $type['params'] ?? [];

        foreach ($params as $key => $param) {
            $params[$key] = static::normalizeFieldParam($param);
        }

        return $params;
    }

    public static function hydrateFieldClass(array $row): array
    {
        if (!empty($row['class'])) {
            return $row;
        }

        $row['class'] = static::normalizeFieldType((string)($row['field_type'] ?? ''));
        return $row;
    }

    public static function hydrateFieldClasses(array $rows): array
    {
        foreach ($rows as $key => $row) {
            if (is_array($row)) {
                $rows[$key] = static::hydrateFieldClasses($row);
                $rows[$key] = static::hydrateFieldClass($rows[$key]);
            }
        }

        return $rows;
    }

    protected static function tables(): array
    {
        if (static::$tables !== null) {
            return static::$tables;
        }

        $tables = [];
        foreach (static::discoverTableClasses() as $class) {
            $table = static::normalizeTable($class::definition(), $class::name());
            $tables[$table['name']] = $table;
        }

        static::$tables = $tables;
        return static::$tables;
    }

    protected static function discoverFieldTypeClasses(): array
    {
        $classes = [];
        foreach (static::phpFiles(SF_CORE_ROOT_PATH . '/Admin/Fields') as $file) {
            $class = 'Simflex\\Admin\\Fields\\' . pathinfo($file, PATHINFO_FILENAME);
            if ($class !== Field::class && is_subclass_of($class, Field::class)) {
                $classes[] = $class;
            }
        }

        foreach (static::phpFiles(SF_ROOT_PATH . '/Admin/Fields') as $file) {
            require_once $file;
            $class = 'App\\Admin\\Fields\\' . pathinfo($file, PATHINFO_FILENAME);
            if (is_subclass_of($class, Field::class)) {
                $classes[] = $class;
            }
        }

        return array_values(array_unique($classes));
    }

    protected static function discoverTableClasses(): array
    {
        $classes = [];
        foreach (static::phpFiles(SF_CORE_ROOT_PATH . '/Admin/Structure/Tables') as $file) {
            $class = 'Simflex\\Admin\\Structure\\Tables\\' . pathinfo($file, PATHINFO_FILENAME);
            if (is_subclass_of($class, Table::class)) {
                $classes[] = $class;
            }
        }

        foreach (static::phpFiles(SF_ROOT_PATH . '/Admin/Structure/Tables') as $file) {
            require_once $file;
            $class = 'App\\Admin\\Structure\\Tables\\' . pathinfo($file, PATHINFO_FILENAME);
            if (is_subclass_of($class, Table::class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    protected static function phpFiles(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        foreach (scandir($dir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = $dir . '/' . $file;
            }
        }

        sort($files);
        return $files;
    }

    protected static function normalizeTable(TableDefinition|array $table, ?string $name = null): array
    {
        if ($table instanceof TableDefinition) {
            $table = $table->toArray();
        }

        $table['name'] = $table['name'] ?? $name;
        $table['order_by'] = $table['order_by'] ?? '';
        $table['order_desc'] = $table['order_desc'] ?? false;
        $table['priv_add'] = $table['priv_add'] ?? null;
        $table['priv_edit'] = $table['priv_edit'] ?? null;
        $table['priv_delete'] = $table['priv_delete'] ?? null;
        $table['class'] = $table['class'] ?? '';

        return $table;
    }

    protected static function normalizeField(FieldDefinition|array $field, string $table, ?string $name = null): array
    {
        if ($field instanceof FieldDefinition) {
            $field = $field->toArray();
        }

        $field['name'] = $field['name'] ?? $name;
        $field['npp'] = $field['npp'] ?? 500;
        $field['label'] = $field['label'] ?? ucfirst((string)$field['name']);
        $field['help'] = $field['help'] ?? '';
        $field['placeholder'] = $field['placeholder'] ?? '';
        $field['table'] = $table;
        $field['class'] = static::normalizeFieldType((string)($field['class'] ?? $field['type'] ?? ''));
        $field['params'] = static::normalizeParams($field['params'] ?? []);

        return $field;
    }

    protected static function normalizeParam(ParamDefinition|array $param, string $table, ?string $name = null): array
    {
        if ($param instanceof ParamDefinition) {
            $param = $param->toArray();
        }

        $param['name'] = $param['name'] ?? $name;
        $param['param_id'] = $param['param_id'] ?? static::stableId($table . '.param.' . $param['name']);
        $param['param_pid'] = $param['param_pid'] ?? '';
        $param['pos'] = $param['pos'] ?? 'left';
        $param['label'] = $param['label'] ?? ucfirst((string)$param['name']);
        $param['default_value'] = $param['default_value'] ?? '';
        $param['table'] = $table;
        $param['class'] = static::normalizeFieldType((string)($param['class'] ?? $param['type'] ?? ''));
        $param['params'] = static::normalizeParams($param['params'] ?? []);

        return $param;
    }

    protected static function normalizeFieldParam(array $param): array
    {
        $param['class'] = static::normalizeFieldType((string)($param['class'] ?? $param['type'] ?? ''));
        $param['help'] = $param['help'] ?? '';
        $param['default_value'] = $param['default_value'] ?? '';

        return $param;
    }

    protected static function normalizeFieldType(string $type): string
    {
        if ($type === '') {
            return '';
        }
        if (str_contains($type, '\\')) {
            return ltrim($type, '\\');
        }

        return 'Simflex\\Admin\\Fields\\' . $type;
    }

    protected static function normalizeParams(array|string $params): array
    {
        if (is_string($params)) {
            $params = unserialize($params) ?: [];
        }

        return isset($params['main']) ? $params : ['main' => $params];
    }

    protected static function stableId(string $value): int
    {
        return (int)sprintf('%u', crc32($value));
    }
}

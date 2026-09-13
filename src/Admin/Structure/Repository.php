<?php

namespace Simflex\Admin\Structure;

use Simflex\Admin\Fields\Field;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\ParamDefinition;

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
        foreach ($tableData['fields'] as $field) {
            $out[] = static::normalizeField($field, $table);
        }

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
        foreach ($tableData['params'] as $param) {
            $param = static::normalizeParam($param, $table);
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
        foreach (static::tableClasses() as $class) {
            $instance = new $class();
            $tables[$instance->name] = $instance->toArray();
        }

        static::$tables = $tables;
        return static::$tables;
    }

    /**
     * @return class-string<Table>[]
     */
    protected static function tableClasses(): array
    {
        $classes = require SF_CORE_ROOT_PATH . '/Admin/Structure/tables.php';

        $appFile = SF_ROOT_PATH . '/Admin/Structure/tables.php';
        if (is_file($appFile)) {
            $classes = array_merge($classes, require $appFile);
        }

        return array_values($classes);
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

    protected static function normalizeField(FieldDefinition $field, string $table): array
    {
        $field = $field->toArray();
        $field['label'] = $field['label'] ?? ucfirst($field['name']);
        $field['table'] = $table;
        $field['class'] = static::normalizeFieldType($field['class']);

        return $field;
    }

    protected static function normalizeParam(ParamDefinition $param, string $table): array
    {
        $param = $param->toArray();
        $param['param_id'] = $param['param_id'] ?? static::stableId($table . '.param.' . $param['name']);
        $param['label'] = $param['label'] ?? ucfirst($param['name']);
        $param['table'] = $table;
        $param['class'] = static::normalizeFieldType((string)($param['class'] ?? ''));

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

    protected static function stableId(string $value): int
    {
        return (int)sprintf('%u', crc32($value));
    }
}

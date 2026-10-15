<?php

namespace Simflex\Core;

/**
 * Components and modules declared in code: the app's provider/components.php and provider/modules.php.
 * menu.component and module_item.module store the class names listed there.
 */
class ExtensionRegistry
{
    /** @var array<string, string>|null */
    protected static ?array $components = null;
    /** @var array<string, array{name: string, type: string, postexec: bool}>|null */
    protected static ?array $modules = null;

    /**
     * @return array<string, string> component class => name
     */
    public static function components(): array
    {
        if (static::$components === null) {
            static::$components = [];
            foreach (static::load('components') as $class => $name) {
                static::$components[ltrim($class, '\\')] = (string)$name;
            }
        }

        return static::$components;
    }

    public static function componentName(string $class): string
    {
        return static::components()[ltrim($class, '\\')] ?? '';
    }

    /**
     * @param string|null $type site|admin, all modules when null
     * @return array<string, array{name: string, type: string, postexec: bool}> module class => settings
     */
    public static function modules(?string $type = null): array
    {
        if (static::$modules === null) {
            static::$modules = [];
            foreach (static::load('modules') as $class => $data) {
                static::$modules[ltrim($class, '\\')] = [
                    'name' => (string)($data['name'] ?? $class),
                    'type' => (string)($data['type'] ?? 'site'),
                    'postexec' => !empty($data['postexec']),
                ];
            }
        }

        if ($type === null) {
            return static::$modules;
        }

        return array_filter(static::$modules, fn(array $module) => $module['type'] === $type);
    }

    /**
     * @return array{name: string, type: string, postexec: bool}|null
     */
    public static function module(string $class): ?array
    {
        return static::modules()[ltrim($class, '\\')] ?? null;
    }

    public static function reset(): void
    {
        static::$components = null;
        static::$modules = null;
    }

    protected static function load(string $name): array
    {
        $file = SF_ROOT_PATH . "/provider/$name.php";
        $data = is_file($file) ? require $file : [];

        return is_array($data) ? $data : [];
    }
}

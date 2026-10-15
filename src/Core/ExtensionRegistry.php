<?php

namespace Simflex\Core;

/**
 * Components and modules declared in code: the lists of classes in the app's provider/components.php and
 * provider/modules.php; their settings are static methods of the classes.
 * menu.component and module_item.module store the class names listed there.
 */
class ExtensionRegistry
{
    /** @var array<class-string<ComponentBase>, string>|null */
    protected static ?array $components = null;
    /** @var array<class-string<ModuleBase>, array{name: string, type: ModuleType, postexec: bool}>|null */
    protected static ?array $modules = null;

    /**
     * @return array<class-string<ComponentBase>, string> component class => name, see ExtensionBase::extensionTitle()
     */
    public static function components(): array
    {
        if (static::$components === null) {
            static::$components = [];
            foreach (static::load('components', ComponentBase::class) as $class) {
                static::$components[$class] = $class::extensionTitle();
            }
        }

        return static::$components;
    }

    public static function componentName(string $class): string
    {
        return static::components()[ltrim($class, '\\')] ?? '';
    }

    /**
     * @param ModuleType|null $type all modules when null
     * @return array<class-string<ModuleBase>, array{name: string, type: ModuleType, postexec: bool}> module class => settings,
     *         see ExtensionBase::extensionTitle(), ModuleBase::moduleType(), ModuleBase::modulePostexec()
     */
    public static function modules(?ModuleType $type = null): array
    {
        if (static::$modules === null) {
            static::$modules = [];
            foreach (static::load('modules', ModuleBase::class) as $class) {
                static::$modules[$class] = [
                    'name' => $class::extensionTitle(),
                    'type' => $class::moduleType(),
                    'postexec' => $class::modulePostexec(),
                ];
            }
        }

        if ($type === null) {
            return static::$modules;
        }

        return array_filter(static::$modules, fn(array $module) => $module['type'] === $type);
    }

    /**
     * @return array{name: string, type: ModuleType, postexec: bool}|null
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

    /**
     * @template T of ExtensionBase
     * @param class-string<T> $base
     * @return list<class-string<T>> classes listed in provider/$name.php
     */
    protected static function load(string $name, string $base): array
    {
        $file = SF_ROOT_PATH . "/provider/$name.php";
        $data = is_file($file) ? require $file : [];

        $classes = [];
        foreach (is_array($data) ? $data : [] as $class) {
            $class = ltrim((string)$class, '\\');
            if (is_subclass_of($class, $base)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}

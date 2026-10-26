<?php

namespace Simflex\Core;

/**
 * Компоненты и модули, объявленные в коде: списки классов в provider/components.php и provider/modules.php
 * приложения; их настройки - статические методы классов.
 * menu.component и module_item.module хранят имена перечисленных там классов.
 */
class ExtensionRegistry
{
    /** @var array<class-string<ComponentBase>, string>|null */
    protected static ?array $components = null;
    /** @var array<class-string<ModuleBase>, array{name: string, postexec: bool}>|null */
    protected static ?array $modules = null;

    /**
     * @return array<class-string<ComponentBase>, string> класс компонента => название, см.
     *        ExtensionBase::extensionTitle()
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
     * @return array<class-string<ModuleBase>, array{name: string, postexec: bool}> класс модуля => настройки,
     *         см. ExtensionBase::extensionTitle(), ModuleBase::modulePostexec()
     */
    public static function modules(): array
    {
        if (static::$modules === null) {
            static::$modules = [];
            foreach (static::load('modules', ModuleBase::class) as $class) {
                static::$modules[$class] = [
                    'name' => $class::extensionTitle(),
                    'postexec' => $class::modulePostexec(),
                ];
            }
        }

        return static::$modules;
    }

    /**
     * @return array{name: string, postexec: bool}|null
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
     * @return list<class-string<T>> классы, перечисленные в provider/$name.php
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

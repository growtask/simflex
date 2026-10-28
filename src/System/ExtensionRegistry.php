<?php

namespace Simflex\Core\System;

use Simflex\Core\Http\ComponentBase;

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
     * @return list<class-string<T>> классы расширений и перечисленные в provider/$name.php сайта
     */
    protected static function load(string $name, string $base): array
    {
        $normalize = fn(array $classes) => array_map(fn($class) => ltrim((string)$class, '\\'), $classes);

        $file = Container::getPaths()->getRoot("provider/$name.php");
        $data = is_file($file) ? require $file : [];
        $app = $normalize(is_array($data) ? $data : []);

        // классы расширений (ExtensionConfig::getComponents()/getModules()); сайт может заменить их наследниками
        $extension = [];
        foreach (ExtensionConfig::getAll() as $config) {
            $extension = array_merge(
                $extension,
                $normalize($name === 'components' ? $config->getComponents() : $config->getModules())
            );
        }

        return array_values(array_filter(
            ExtensionConfig::mergeClasses($extension, $app),
            fn(string $class) => is_subclass_of($class, $base)
        ));
    }
}

<?php

namespace Simflex\Admin;

use Simflex\Admin\Modules\Breadcrumbs\Breadcrumbs;
use Simflex\Admin\Modules\Menu\Menu as MenuModule;
use Simflex\Core\CodeRegistry;

/**
 * Модули макета админки, объявленные в коде. Модули сайта размещаются в таблице module_item.
 *
 * Приложение добавляет свои модули в подклассе, указанном в конфиге (adminModulesClass): init() с вызовом
 * parent::init() и add() / remove(). Модули одной позиции идут в порядке добавления.
 */
class AdminModules extends CodeRegistry
{
    /** @var list<AdminModule>|null */
    private static ?array $modules = null;

    /**
     * Объявляет модули ядра. Подкласс приложения вызывает parent::init() и добавляет или удаляет свои
     */
    protected static function init(): void
    {
        static::add(new AdminModule(MenuModule::class, 'menu', 'Меню'));
        static::add(new AdminModule(Breadcrumbs::class, 'breadcrumbs', 'Хлебные крошки'));
    }

    protected static function add(AdminModule $module): void
    {
        self::$modules[] = $module;
    }

    /**
     * Удаляет все размещения класса модуля
     */
    protected static function remove(string $class): void
    {
        self::$modules = array_values(array_filter(
            self::$modules,
            fn(AdminModule $module) => $module->class !== ltrim($class, '\\')
        ));
    }

    /**
     * @return list<AdminModule> модули ядра и приложения, по порядку
     */
    public static function getModules(): array
    {
        if (self::$modules === null) {
            self::$modules = [];
            $class = static::registryClass();
            $class::init();
        }

        return self::$modules;
    }

    /**
     * Модули, показываемые в разделе
     *
     * @param string $section Ссылка текущего пункта меню, как она объявлена в Menu, '' вне меню
     * @return list<AdminModule>
     */
    public static function getForSection(string $section): array
    {
        return array_values(array_filter(
            static::getModules(),
            fn(AdminModule $module) => !$module->sections || in_array($section, $module->sections, true)
        ));
    }

    protected static function configProperty(): string
    {
        return 'adminModulesClass';
    }

    public static function reset(): void
    {
        self::$modules = null;
    }
}

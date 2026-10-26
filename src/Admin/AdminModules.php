<?php

namespace Simflex\Admin;

use Simflex\Admin\Modules\Breadcrumbs\Breadcrumbs;
use Simflex\Admin\Modules\Menu\Menu as MenuModule;
use Simflex\Core\CodeRegistry;

/**
 * Modules of the admin panel layout declared in code. The site modules are placed in the module_item table.
 *
 * The app adds its modules in a subclass named in the config (adminModulesClass): init() calling parent::init()
 * and add() / remove(). Modules of a position go in the order they are added.
 */
class AdminModules extends CodeRegistry
{
    /** @var list<AdminModule>|null */
    private static ?array $modules = null;

    /**
     * Declares the core modules. The app's subclass calls parent::init() and adds or removes its own
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
     * Removes all the placements of the module class
     */
    protected static function remove(string $class): void
    {
        self::$modules = array_values(array_filter(
            self::$modules,
            fn(AdminModule $module) => $module->class !== ltrim($class, '\\')
        ));
    }

    /**
     * @return list<AdminModule> the core and the app ones, in order
     */
    public static function all(): array
    {
        if (self::$modules === null) {
            self::$modules = [];
            $class = static::registryClass();
            $class::init();
        }

        return self::$modules;
    }

    /**
     * Modules shown in the section
     *
     * @param string $section Link of the current menu item as declared in Menu, '' outside the menu
     * @return list<AdminModule>
     */
    public static function forSection(string $section): array
    {
        return array_values(array_filter(
            static::all(),
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

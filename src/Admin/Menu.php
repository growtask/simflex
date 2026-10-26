<?php

namespace Simflex\Admin;

use Simflex\Admin\Structure\Handlers;
use Simflex\Core\CodeRegistry;
use Simflex\Core\Privilege;

/**
 * Admin panel menu declared in code. An item is identified by its link.
 *
 * The app adds its sections in a subclass named in the config (adminMenuClass): init() calling parent::init()
 * and add() / remove(). Items go in the order they are added, add(..., before: link) puts one before another.
 */
class Menu extends CodeRegistry
{
    /** @var array<string, MenuItem>|null link => item, subitems in $parents */
    private static ?array $items = null;

    /** @var array<string, string> link => parent link, '' for the root items */
    private static array $parents = [];

    /**
     * Declares the core sections. The app's subclass calls parent::init() and adds or removes its own
     */
    protected static function init(): void
    {
        static::add(new MenuItem('Аккаунт', '/account', handler: Handlers\Account::class, icon: 'user', hidden: true));

        static::add(new MenuItem('Управление', '/admin', icon: 'home', privilege: Privilege::DEV, children: [
            new MenuItem('Привилегии', '/admin/privilege', handler: Handlers\Privilege::class, icon: 'lock-mini'),
            new MenuItem('Роли', '/admin/role', handler: Handlers\UserRole::class, icon: 'users-mini'),
            new MenuItem(
                'Привилегии ролей',
                '/admin/role_privilege',
                handler: Handlers\UserRolePrivilege::class,
                icon: 'file-shield-mini',
                privilege: Privilege::DEV,
            ),
            new MenuItem('Пользователи', '/admin/user', handler: Handlers\User::class, icon: 'user-mini'),
            new MenuItem(
                'Персональные привилегии',
                '/admin/privilege_personal',
                handler: Handlers\UserPrivilegePersonal::class,
                icon: 'fingerprint-mini',
                privilege: Privilege::DEV,
            ),
            new MenuItem('Cron', '/cron', handler: Handlers\Cron::class, icon: 'calendar-mini'),
        ]));

        static::add(new MenuItem('Настройки сайта', '/site', icon: 'settings', children: [
            new MenuItem('Меню', '/site/menu', handler: Handlers\Menu::class, icon: 'container-mini'),
            new MenuItem('Модули', '/site/module', handler: Handlers\ModuleItem::class, icon: 'puzzle-mini'),
            new MenuItem('Настройки', '/site/settings', handler: Handlers\Settings::class, icon: 'settings-mini'),
            new MenuItem('SEO', '/site/seo', handler: Handlers\Seo::class, icon: 'chart-mini'),
        ]));

        static::add(new MenuItem('Редактор страниц', '/content', handler: Handlers\Content::class, icon: 'edit'));
    }

    /**
     * Adds an item with its subitems. An item with the same link is replaced
     *
     * @param string $parent Link of the parent item, '' for a root one
     * @param string|null $before Link of the sibling to put the item before, null — after the others
     */
    protected static function add(MenuItem $item, string $parent = '', ?string $before = null): void
    {
        if ($parent !== '' && !isset(self::$items[$parent])) {
            throw new \LogicException("Admin menu: no parent item $parent for $item->link");
        }

        if ($before !== null && isset(self::$items[$before])) {
            unset(self::$items[$item->link]);
            $items = [];
            foreach (self::$items as $link => $existing) {
                if ($link === $before) {
                    $items[$item->link] = $item;
                }
                $items[$link] = $existing;
            }
            self::$items = $items;
        } else {
            self::$items[$item->link] = $item;
        }
        self::$parents[$item->link] = $parent;

        foreach ($item->children as $child) {
            static::add($child, $item->link);
        }
    }

    /**
     * Removes an item with its subitems
     */
    protected static function remove(string $link): void
    {
        foreach (array_keys(self::$parents, $link, true) as $child) {
            static::remove($child);
        }
        unset(self::$items[$link], self::$parents[$link]);
    }

    /**
     * @return array<string, MenuItem> link => item, the core and the app ones, in order
     */
    public static function all(): array
    {
        if (self::$items === null) {
            self::$items = self::$parents = [];
            $class = static::registryClass();
            $class::init();
        }

        return self::$items;
    }

    /**
     * Link of the parent item, '' for a root one
     */
    public static function parent(string $link): string
    {
        static::all();
        return self::$parents[$link] ?? '';
    }

    protected static function configProperty(): string
    {
        return 'adminMenuClass';
    }

    public static function reset(): void
    {
        self::$items = null;
        self::$parents = [];
    }
}

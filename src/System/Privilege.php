<?php

namespace Simflex\Core\System;

use Simflex\Core\Models\UserPrivilege;

/**
 * Привилегии пользователей, объявленные в коде. Проверяются по имени: User::ican(Privilege::ADMIN).
 *
 * Приложение добавляет свои привилегии в подклассе, указанном в конфиге (privilegeClass): новые константы для имен
 * и init(), вызывающий parent::init() и define(). Ядро вызывает Privilege::... и получает реестр приложения.
 *
 * Таблица user_privilege необязательна: в ней хранится активность, переключенная в админке, привилегия без записи
 * берет ее из кода. Роли, пользователи, пункты меню и виджеты ссылаются на привилегию по имени.
 */
class Privilege extends CodeRegistry
{
    /** Разработчик: полный доступ, есть все привилегии, даже выключенные. Выключить нельзя */
    public const DEV = 'dev';

    /** Доступ в админку. Если выключить, админка закрывается для всех, кроме разработчиков */
    public const ADMIN = 'admin';

    /** @var array<string, PrivilegeParams>|null имя => параметры */
    private static ?array $registry = null;

    /** @var array<string, bool>|null имя => активность, переключенная в админке */
    private static ?array $switched = null;

    /**
     * Объявляет привилегии. Подкласс приложения вызывает parent::init() и добавляет или переопределяет свои
     */
    protected static function init(): void
    {
        static::define(self::DEV, new PrivilegeParams('Привилегия разработчика: полный доступ', switchable: false));
        static::define(self::ADMIN, new PrivilegeParams('Привилегия администратора: доступ к админке'));

        // привилегии расширений; сайт может переопределить их в своём наследнике после parent::init()
        foreach (ExtensionConfig::getAll() as $config) {
            foreach ($config->getPrivileges() as $name => $params) {
                static::define($name, $params);
            }
        }
    }

    protected static function define(string $name, PrivilegeParams $params): void
    {
        self::$registry[$name] = $params;
    }

    /**
     * @return array<string, PrivilegeParams> имя => параметры, ядра и приложения
     */
    public static function getAll(): array
    {
        if (self::$registry === null) {
            self::$registry = [];
            $class = static::registryClass();
            $class::init();
        }

        return self::$registry;
    }

    public static function getParams(string $name): ?PrivilegeParams
    {
        return static::getAll()[$name] ?? null;
    }

    /**
     * Объявлена в коде и включена: активность, переключенная в админке, иначе значение по умолчанию из кода
     */
    public static function isActive(string $name): bool
    {
        $params = static::getParams($name);
        if (!$params) {
            return false;
        }
        if (!$params->switchable) {
            return true;
        }

        return static::switched()[$name] ?? $params->active;
    }

    /**
     * Сохраняет активность, переключенную в админке
     */
    public static function setActive(string $name, bool $active): bool
    {
        $params = static::getParams($name);
        if (!$params || !$params->switchable) {
            return false;
        }

        UserPrivilege::upsert(['name' => $name, 'active' => (int)$active], ['active']);
        self::$switched = null;
        return true;
    }

    /**
     * @param string[] $names
     * @return string[] объявленные и включенные
     */
    public static function filterActive(array $names): array
    {
        return array_values(array_filter(array_unique($names), fn($name) => static::isActive((string)$name)));
    }

    /**
     * Привилегии, которые реально есть у пользователя
     *
     * @param string[] $names назначенные роли пользователя и ему лично
     * @return string[] для разработчика все объявленные, иначе назначенные из объявленных и включенных
     */
    public static function granted(array $names): array
    {
        return in_array(self::DEV, $names, true) ? array_keys(static::getAll()) : static::filterActive($names);
    }

    protected static function configProperty(): string
    {
        return 'privilegeClass';
    }

    /**
     * @return array<string, bool>
     */
    protected static function switched(): array
    {
        if (self::$switched === null) {
            self::$switched = [];
            foreach (UserPrivilege::findAdv()->asArray()->select(['name', 'active'])->all() as $row) {
                self::$switched[$row['name']] = (bool)$row['active'];
            }
        }

        return self::$switched;
    }

    public static function reset(): void
    {
        self::$registry = self::$switched = null;
    }
}

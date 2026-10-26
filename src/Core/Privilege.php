<?php

namespace Simflex\Core;

/**
 * User privileges declared in code. Checked by name: User::ican(Privilege::ADMIN).
 *
 * The app adds its privileges in a subclass named in the config (privilegeClass): new constants for the names
 * and init() calling parent::init() and define(). The core calls Privilege::... and gets the app's registry.
 *
 * The user_privilege table is optional: it keeps the activity switched in the admin panel, a privilege without a row
 * takes it from code. Roles, users, menu items and widgets refer to a privilege by its name.
 */
class Privilege extends CodeRegistry
{
    /** Developer: full access, has all the privileges, even the switched off ones. Can't be switched off */
    public const DEV = 'dev';

    /** Access to the admin panel. Switching it off closes the admin panel for everybody but the developers */
    public const ADMIN = 'admin';

    /** @var array<string, PrivilegeParams>|null name => params */
    private static ?array $registry = null;

    /** @var array<string, bool>|null name => activity switched in the admin panel */
    private static ?array $switched = null;

    /**
     * Declares the privileges. The app's subclass calls parent::init() and adds or redefines its own
     */
    protected static function init(): void
    {
        static::define(self::DEV, new PrivilegeParams('Привилегия разработчика: полный доступ', switchable: false));
        static::define(self::ADMIN, new PrivilegeParams('Привилегия администратора: доступ к админке'));
    }

    protected static function define(string $name, PrivilegeParams $params): void
    {
        self::$registry[$name] = $params;
    }

    /**
     * @return array<string, PrivilegeParams> name => params, the core and the app ones
     */
    public static function all(): array
    {
        if (self::$registry === null) {
            self::$registry = [];
            $class = static::registryClass();
            $class::init();
        }

        return self::$registry;
    }

    public static function params(string $name): ?PrivilegeParams
    {
        return static::all()[$name] ?? null;
    }

    /**
     * Declared in code and enabled: the activity switched in the admin panel, otherwise the default from code
     */
    public static function isActive(string $name): bool
    {
        $params = static::params($name);
        if (!$params) {
            return false;
        }
        if (!$params->switchable) {
            return true;
        }

        return static::switched()[$name] ?? $params->active;
    }

    /**
     * Keeps the activity switched in the admin panel
     */
    public static function setActive(string $name, bool $active): bool
    {
        $params = static::params($name);
        if (!$params || !$params->switchable) {
            return false;
        }

        DB::query(
            'INSERT INTO user_privilege (name, active) VALUES (?, ?) ON DUPLICATE KEY UPDATE active = VALUES(active)',
            [$name, (int)$active]
        );
        self::$switched = null;
        return true;
    }

    /**
     * @param string[] $names
     * @return string[] the declared and enabled ones
     */
    public static function filterActive(array $names): array
    {
        return array_values(array_filter(array_unique($names), fn($name) => static::isActive((string)$name)));
    }

    /**
     * Privileges the user actually has
     *
     * @param string[] $names assigned to the user's role and to the user personally
     * @return string[] all declared ones for a developer, otherwise the declared and enabled assigned ones
     */
    public static function granted(array $names): array
    {
        return in_array(self::DEV, $names, true) ? array_keys(static::all()) : static::filterActive($names);
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
            foreach (DB::assoc('SELECT name, active FROM user_privilege') ?: [] as $row) {
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

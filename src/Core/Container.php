<?php


namespace Simflex\Core;

use Simflex\Core\Core;
use Simflex\Core\Page;
use Simflex\Core\User;

/**
 * Class Container
 *
 * @package App\Core
 * @method static ConfigBase getConfig
 * @method static Page getPage
 * @method static Core getCore
 * @method static \Simflex\Core\Request getRequest
 * @method static \Simflex\Core\Response getResponse
 * @method static User getUserLegacy
 * @method static Factory getFactory
 * @method static EventManager getEvents
 */
class Container
{
    protected static array $registry = [];

    public static function set(string $name, mixed $payload): void
    {
        static::$registry[$name] = $payload;
    }

    public static function get(string $name): mixed
    {
        return static::$registry[$name] ?? null;
    }

    public static function isSet(string $name): bool
    {
        return isset(static::$registry[$name]);
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (strpos($name, 'get') === 0) {
            return static::get(lcfirst(substr($name, 3)));
        }

        throw new \BadMethodCallException();
    }

    public static function setAuthHandler(callable $closure): void
    {
        static::$registry['authHandler'] = $closure;
    }

    public static function getUser(): ?\Simflex\Core\Models\User
    {
        if (
            !($called =& static::$registry['authHandlerCalled'])
            && ($authHandler =& static::$registry['authHandler'])
            && is_callable($authHandler)
        ) {
            $authHandler();
            $called = true;
        }
        return static::get('user');
    }
}

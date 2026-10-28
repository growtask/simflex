<?php

namespace Simflex\Core\System;

/**
 * Контейнер сервисов: конфиг, страница, запрос и ответ, пути, окружение, сервисы provider/services.php.
 * Сервис name доступен как Container::getName()
 *
 * @method static ConfigBase getConfig
 * @method static \Simflex\Core\Http\Page|\Simflex\Core\Admin\Page getPage
 * @method static \Simflex\Core\Http\Request getRequest
 * @method static \Simflex\Core\Http\Response getResponse
 * @method static \Simflex\Core\System\DI\Factory getFactory
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

    /**
     * Пути на диске: корень сайта, public/, ядро. Задаёт Core
     */
    public static function getPaths(): Paths
    {
        return static::$registry['paths'] ?? throw new \LogicException('Paths are not set: create Simflex\Core\Kernel first');
    }

    /**
     * Где выполняется код: сайт, админка, API, консоль. Задаёт Core::init()
     */
    public static function getLocation(): Location
    {
        return static::$registry['location'] ?? throw new \LogicException('Location is not set: Core::init() was not called');
    }

    public static function setAuthHandler(callable $closure): void
    {
        static::$registry['authHandler'] = $closure;
    }

    public static function getUser(): ?\Simflex\Core\Models\User
    {
        if (
            !($called = & static::$registry['authHandlerCalled'])
            && ($authHandler = & static::$registry['authHandler'])
            && is_callable($authHandler)
        ) {
            $authHandler();
            $called = true;
        }
        return static::get('user');
    }
}

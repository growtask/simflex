<?php

namespace Simflex\Core;

use JetBrains\PhpStorm\Deprecated;
use Simflex\Core\DB\MySQL;

/**
 * Base configuration class
 */
abstract class ConfigBase
{
    /**
     * @var array{
     *     adapter: string,
     *     host: string,
     *     user: string,
     *     password: string,
     *     name: string
     * }
     *     Database options
     */
    public array $db = [
        'adapter' => MySQL::class,
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'name' => 'simflex',
    ];

    /**
     * @var string Default component to load
     */
    public string $defaultComponent = '\Simflex\Extensions\Content\Content';

    /**
     * @var string Default theme to load
     */
    #[Deprecated]
    public string $theme = 'default';

    /**
     * @var bool Enable dev tools
     */
    public bool $devMode = false;

    /**
     * @var bool Enable logging, despite of dev tools
     */
    public bool $enableLogging = false;

    /**
     * @var string Log path
     */
    public string $logPath = SF_ROOT_PATH . '/uf/log';

    /**
     * @var array{routes: string, events: string, services: string} Data providers for autoloading
     */
    public array $files = [
        'routes' => SF_CORE_ROOT_PATH . '/routes.php',
        'events' => SF_CORE_ROOT_PATH . '/events.php',
        'services' => SF_CORE_ROOT_PATH . '/services.php',
    ];

    /**
     * @var array Extra, user-defined config data
     */
    public array $extra = [];

    /**
     * Getter for user-defined data
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->extra[$name] ?? null;
    }

    /**
     * Load config data
     * @return void
     */
    public abstract function load(): void;
}

<?php

namespace Simflex\Core;

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
        'host' => '127.0.0.1',
        'user' => 'simflex',
        'password' => 'simflex',
        'name' => 'simflex',
    ];

    /**
     * @var array{
     *     transport: string,
     *     host: string,
     *     port: string,
     *     security: string,
     *     username: string,
     *     password: string,
     * } Mail options. "security" field value may differ per transport, check what exactly the transport you want to use
     * expects. For PHPMailer, use these: https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#property_SMTPSecure
     *
     * Set the class to the "transport" field. Note that it MUST be implementing Transport interface.
     */
    public array $mail = [
        'transport' => null,
        'host' => 'localhost',
        'port' => '25',
        'security' => '',
        'username' => '',
        'password' => '',
    ];

    /**
     * @var array{
     *     token: string
     * } Telegram options
     */
    public array $telegram = [
        'token' => '',
    ];

    /**
     * @var string Default component to load
     */
    public string $defaultComponent = '\Simflex\Extensions\Content\Content';

    /**
     * @var string Class declaring the user privileges: \Simflex\Core\Privilege or the app's subclass
     */
    public string $privilegeClass = Privilege::class;

    /**
     * @var string Class declaring the admin panel menu: \Simflex\Admin\Menu or the app's subclass
     */
    public string $adminMenuClass = \Simflex\Admin\Menu::class;

    /**
     * @var string Class declaring the modules of the admin panel layout: \Simflex\Admin\AdminModules or the app's subclass
     */
    public string $adminModulesClass = \Simflex\Admin\AdminModules::class;

    /**
     * @var string Default theme to load
     */
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
     * @var string Admin panel URL prefix, e.g. /admin. Admin menu links are declared relative to it
     */
    public string $adminPath = '/admin';

    /**
     * @var bool Also write the log to a file in $logPath (by default the log goes to stdout only)
     */
    public bool $logToFile = false;

    /**
     * @var string Log path, used when $logToFile is enabled
     */
    public string $logPath = SF_ROOT_PATH . '/log';

    /**
     * @var array{routes: string, events: string, services: string} Data providers for autoloading
     */
    public array $files = [
        'routes' => SF_CORE_ROOT_PATH . '/routes.php',
        'events' => SF_CORE_ROOT_PATH . '/events.php',
        'services' => SF_CORE_ROOT_PATH . '/services.php',
        'commands' => SF_CORE_ROOT_PATH . '/commands.php',
    ];

    /**
     * @var array Extra, user-defined config data
     */
    public array $extra = [];

    /**
     * Getter for user-defined data
     */
    public function __get(string $name): mixed
    {
        return $this->extra[$name] ?? null;
    }

    public function getRoutes(): array
    {
        return include $this->files['routes'];
    }

    public function getEvents(): array
    {
        return include $this->files['events'];
    }

    public function getServices(): array
    {
        return include $this->files['services'];
    }

    public function getCommands(): array
    {
        return include $this->files['commands'];
    }

    /**
     * Load config data
     */
    abstract public function load(): void;
}

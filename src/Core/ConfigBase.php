<?php

namespace Simflex\Core;

use Simflex\Core\DB\MySQL;

/**
 * Базовый класс конфигурации
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
     *     Параметры базы данных
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
     * } Параметры почты. Значение поля "security" зависит от транспорта, проверьте, что именно ожидает
     * нужный транспорт. Для PHPMailer подходят эти:
     * https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#property_SMTPSecure
     *
     * В поле "transport" указывается класс. Он ОБЯЗАН реализовывать интерфейс Transport.
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
     * } Параметры Telegram
     */
    public array $telegram = [
        'token' => '',
    ];

    /**
     * @var string Компонент, загружаемый по умолчанию
     */
    public string $defaultComponent = '\Simflex\Extensions\Content\Content';

    /**
     * @var string Класс, объявляющий привилегии пользователей: \Simflex\Core\Privilege или подкласс приложения
     */
    public string $privilegeClass = Privilege::class;

    /**
     * @var string Класс, объявляющий меню админки: \Simflex\Admin\Menu или подкласс приложения
     */
    public string $adminMenuClass = \Simflex\Admin\Menu::class;

    /**
     * @var string Класс, объявляющий модули макета админки: \Simflex\Admin\AdminModules или подкласс приложения
     */
    public string $adminModulesClass = \Simflex\Admin\AdminModules::class;

    /**
     * @var string Тема по умолчанию
     */
    public string $theme = 'default';

    /**
     * @var bool Включить инструменты разработчика
     */
    public bool $devMode = false;

    /**
     * @var bool Включить логирование независимо от инструментов разработчика
     */
    public bool $enableLogging = false;

    /**
     * @var string Префикс URL админки, например /admin. Ссылки меню админки задаются относительно него
     */
    public string $adminPath = '/admin';

    /**
     * @var bool Писать лог еще и в файл в $logPath (по умолчанию лог идет только в stdout)
     */
    public bool $logToFile = false;

    /**
     * @var string Путь к логу, используется при включенном $logToFile
     */
    public string $logPath = SF_ROOT_PATH . '/log';

    /**
     * @var array{routes: string, events: string, services: string} Провайдеры данных для автозагрузки
     */
    public array $files = [
        'routes' => SF_CORE_ROOT_PATH . '/routes.php',
        'events' => SF_CORE_ROOT_PATH . '/events.php',
        'services' => SF_CORE_ROOT_PATH . '/services.php',
        'commands' => SF_CORE_ROOT_PATH . '/commands.php',
    ];

    /**
     * @var array Дополнительные пользовательские данные конфига
     */
    public array $extra = [];

    /**
     * Получение пользовательских данных
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
     * Загружает данные конфига
     */
    abstract public function load(): void;
}

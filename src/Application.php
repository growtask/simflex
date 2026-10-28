<?php

namespace Simflex;

use Dotenv\Dotenv;
use Simflex\Admin\Core as AdminCore;
use Simflex\Auth\Auth\BasicAuthMiddleware;
use Simflex\Auth\Auth\Chain;
use Simflex\Auth\Auth\CookieMiddleware;
use Simflex\Auth\Auth\SessionMiddleware;
use Simflex\Auth\Bootstrap;
use Simflex\Core\ConfigBase;
use Simflex\Console\Cli;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Events\Event;
use Simflex\Core\Events\Events;
use Simflex\Logger\Log;
use Simflex\Logger\FileLogger;
use Simflex\Logger\StdLogger;
use Simflex\Core\Page;
use Simflex\Layout\LayoutManager;
use Simflex\Core\SiteSettings;

/**
 * Приложение: загрузка и запуск сайта.
 *
 * Точки входа сайта создают приложение и вызывают один метод: public/index.php - runHttp(), консоль ./sf -
 * runConsole(). До этого точка входа подключает constants.php сайта (SF_ROOT_PATH, SF_PUBLIC_PATH) и автозагрузчик
 * composer.
 *
 * Загрузка (init()): конфиг сайта (config.php, .env), настройки PHP, сервисы, события, логгер, авторизация, ядро
 * и страница. Каждый этап окружён событиями Events::Pre* / Post* (provider/events.php): чтобы дополнить загрузку,
 * подпишитесь на событие, а не переопределяйте класс
 */
class Application
{
    protected ConfigBase $cfg;

    public function __construct()
    {
        // функции сайта
        if (is_file(SF_ROOT_PATH . '/functions.php')) {
            require_once SF_ROOT_PATH . '/functions.php';
        }
    }

    /**
     * Обрабатывает HTTP-запрос: сайт, админку (адрес - конфиг adminPath) или API файлового менеджера админки
     */
    public function runHttp(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if (!$this->isAdminPath($path)) {
            $this->init(SF_LOCATION_SITE);
            echo $this->prepareOutput($this->execute(true));
            return;
        }

        $this->init(SF_LOCATION_ADMIN);
        if ($path === AdminCore::url('fm')) {
            $this->executeFileManager();
        } else {
            $this->execute(false);
        }
    }

    /**
     * Выполняет консольную команду из аргументов командной строки: ./sf provider/method [args]
     */
    public function runConsole(): void
    {
        $this->init(SF_LOCATION_CLI);
        Cli::run(Container::getRequest());
    }

    /**
     * Относится ли путь запроса к админке (её URL-префикс задан в конфиге: adminPath)
     */
    public function isAdminPath(string $path): bool
    {
        $this->initConfig();
        return AdminCore::isAdminPath($path);
    }

    /**
     * Загружает приложение
     * @param string $location Где выполняется приложение: SF_LOCATION_SITE, SF_LOCATION_ADMIN, SF_LOCATION_API,
     *                         SF_LOCATION_CLI. Задаёт константу SF_LOCATION
     */
    public function init(string $location): void
    {
        define('SF_LOCATION', $location);

        // базовая инициализация
        $this->initConfig();
        $this->initPhp();

        if (SF_LOCATION === SF_LOCATION_CLI) {
            $this->initPhpCli();
        }

        $this->initExceptionHandler();
        $this->initServices();
        $this->initEvents();
        $this->initLogger();

        Log::debug('Simflex Framework v' . getSimflexVersion());

        // подсистемы ядра
        $this->initAuth();
        $this->initCore();

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreInit, static::class));

        if (SF_LOCATION !== SF_LOCATION_CLI) {
            Container::getCore()::init();
            Container::getPage()::init();
            LayoutManager::init();
        }

        $ev->dispatch(new Event(Events::PostInit, static::class));
    }

    /**
     * Выполняет приложение: выводит страницу темы сайта или админки
     * @param bool $asString Вернуть вывод строкой, иначе - отправить в браузер
     * @return string|null Содержимое страницы или null, если $asString равен false
     */
    public function execute(bool $asString): ?string
    {
        if ($asString) {
            ob_start();
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreExecute, static::class));

        Container::getCore()::execute();

        $ev->dispatch(new Event(Events::PostExecute, static::class));
        return $asString ? ob_get_clean() : null;
    }

    /**
     * Обрабатывает запрос к API файлового менеджера админки. Доступно только пользователям админки
     */
    public function executeFileManager(): void
    {
        $mgr = new \Simflex\FileManager\Manager(SF_PUBLIC_PATH . '/uf', function () {
            return (bool)Container::getUser()?->ican(\Simflex\Core\Privilege::ADMIN);
        });

        $mgr->handleRequest();
    }

    /**
     * Готовит страницу сайта к выводу: подставляет настройки сайта ({имя} -> значение, {year} - текущий год),
     * в рабочем режиме сжимает HTML
     */
    public function prepareOutput(string $data): string
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PrePrepareOutput, static::class, $data));

        // параметры сайта всегда берутся из ядра сайта: Admin\SiteSettings::getAll() требует ключ
        // и не может вернуть весь список
        $params = SiteSettings::getAll();
        $params = is_array($params) ? $params : [];
        $params['year'] = date('Y');

        $data = str_replace(
            array_map(fn(int|string $key) => '{' . $key . '}', array_keys($params)),
            array_map('strval', array_values($params)),
            $data
        );

        if (!$this->cfg->devMode) {
            $data = preg_replace([
                '/>[^\S ]+/', // убираем пробельные символы после тегов, кроме пробела
                '/[^\S ]+</', // убираем пробельные символы перед тегами, кроме пробела
                '/(\s)+/', // сокращаем повторяющиеся пробельные символы
                '/<!--(.|\s)*?-->/', // убираем HTML-комментарии
            ], [
                '>',
                '<',
                '\\1',
                '',
            ], $data);
        }

        $ev->dispatch(new Event(Events::PostPrepareOutput, static::class, $data));
        return $data;
    }

    /**
     * Механизмы авторизации: сессия, cookie, Basic Auth - по очереди, при первом обращении к пользователю
     */
    protected function initAuth(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreAuthInit, static::class));

        Container::setAuthHandler(function () {
            Bootstrap::authByMiddlewareChain(
                (new Chain([
                    new SessionMiddleware(),
                    new CookieMiddleware(),
                    new BasicAuthMiddleware(),
                ]))
            );
        });

        $ev->dispatch(new Event(Events::PostAuthInit, static::class));
    }

    /**
     * Ядро и страница: сайта или админки. В консоли не создаются
     */
    protected function initCore(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreCoreInit, static::class));

        if (SF_LOCATION === SF_LOCATION_ADMIN) {
            Container::set('page', new \Simflex\Admin\Page());
            Container::set('core', AdminCore::class);
        } elseif (SF_LOCATION === SF_LOCATION_SITE || SF_LOCATION === SF_LOCATION_API) {
            Container::set('page', new Page());
            Container::set('core', Core::class);
        }

        $ev->dispatch(new Event(Events::PostCoreInit, static::class));
    }

    /**
     * Конфиг сайта: .env и класс App\Config из config.php
     */
    protected function initConfig(): void
    {
        if (isset($this->cfg)) {
            return;
        }

        Dotenv::createImmutable(SF_ROOT_PATH)->safeLoad();

        require_once SF_ROOT_PATH . '/config.php';
        $this->cfg = new \App\Config();
        $this->cfg->load();

        Container::set('config', $this->cfg);
    }

    protected function initExceptionHandler(): void
    {
        // в режиме разработчика ошибки ловит whoops
        if ($this->cfg->devMode) {
            return;
        }

        set_exception_handler(function (\Throwable $ex) {
            Log::emergency("Exception: {ex}\n\nStack trace: {st}", [
                'ex' => $ex->getMessage(),
                'st' => $ex->getTraceAsString(),
            ]);
        });
    }

    protected function initPhp(): void
    {
        date_default_timezone_set($this->cfg->timezone);
        ini_set('default_charset', 'UTF-8');
    }

    protected function initPhpCli(): void
    {
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 600);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    }

    /**
     * Сервисы ядра, расширений и сайта (provider/services.php): общие (all) и для текущего окружения
     */
    protected function initServices(): void
    {
        $services = $this->cfg->getServices();
        $toLoad = $services['all'] ?? [];

        $groups = match (SF_LOCATION) {
            SF_LOCATION_ADMIN, SF_LOCATION_SITE => ['web'],
            SF_LOCATION_API => ['api'],
            SF_LOCATION_CLI => ['cli'],
            default => [],
        };
        if ($this->cfg->devMode) {
            $groups[] = 'dev';
        }
        foreach ($groups as $group) {
            $toLoad = array_merge($toLoad, $services[$group] ?? []);
        }

        foreach ($toLoad as $service) {
            Container::set($service::getServiceName(), new $service());
        }
    }

    protected function initEvents(): void
    {
        Container::getEvents()->subscribeAll($this->cfg->getEvents());
    }

    /**
     * Логгер: DebugBar в режиме разработчика, stdout, файл (конфиг logToFile)
     */
    protected function initLogger(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreLoggerInit, static::class));

        if (!$this->cfg->devMode && !$this->cfg->enableLogging) {
            return;
        }

        if ($this->cfg->devMode) {
            Log::addLogger(Container::get('debugbar')->getMessages());
        }

        Log::addLogger(new StdLogger());

        if ($this->cfg->logToFile) {
            Log::addLogger(new FileLogger());
        }

        $ev->dispatch(new Event(Events::PostLoggerInit, static::class));
    }
}

<?php

namespace Simflex\Core;

use Simflex\Core\DI\Injector;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Html\HtmlResponse;
use Simflex\Core\Models\ModelSettings;
use Simflex\Core\Routing\Resolver;

class Core
{
    protected static array $siteParams = [];
    protected static array $menuTree = [];
    protected static array $menuById = [];
    protected static array $menuByLink = [];
    protected static array $menuByExt = [];
    protected static ?array $menuCur = null;
    protected static bool $contentOnly = false;

    private function __construct()
    {
    }

    public static function init(): void
    {
        // если request или response не заданы, все равно их создаем
        // нужно для обратной совместимости
        if (!Container::isSet('request')) {
            Container::set('request', new HtmlRequest());
        }

        if (!Container::isSet('response')) {
            Container::set('response', new HtmlResponse());
        }

        $request = Container::getRequest();

        if ($request->isAjax()) {
            self::$contentOnly = true;
            $sf_module_id = isset($_REQUEST['sf_module_id']) ? (int)$_REQUEST['sf_module_id'] : 0;
            if ($sf_module_id > 0) {
                Page::moduleById($sf_module_id);
                exit();
            }
        } else {
            $q = 'SELECT title, description, keywords, metatags FROM seo WHERE link = ?';
            if ($row = DB::result($q, '', [$request->getPath()])) {
                Page::seo($row['title'], $row['description'], $row['keywords'], true, $row['metatags']);
            }
        }

        self::$siteParams = ModelSettings::values();

        static::initMenu();
    }

    protected static function initMenu(): void
    {
        $q = 'SELECT *, component AS class
        FROM menu
        WHERE active=1
        ORDER BY npp';

        $rows = DB::assoc($q);
        $path = Container::getRequest()->getPath();
        foreach ($rows as $row) {
            self::$menuTree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            self::$menuById[(int)$row['menu_id']] = $row;
            self::$menuByLink[md5($row['link'])] = $row;
            self::$menuByExt[strtolower(substr($row['class'], 3))][] = $row;
            if ($row['link'] == $path && !self::$menuCur) {
                self::$menuCur = $row;
            }
        }
    }

    /**
     * Значение настройки сайта по алиасу
     *
     * Умеет подставлять значения из массива, например:
     * `value (k = 'greeting') = 'Hello {name}'`, тогда
     * `Core::siteParam('greeting', 'Hello {name}', ['name' => 'John'])` вернет `'Hello John'`
     *
     * Если настройка не найдена и $key задан, она создается в таблице `settings`
     * со значением $defaultValue (скаляр или null) и становится доступной для редактирования в админке.
     *
     * @param string|bool $key false - получить все настройки, иначе алиас
     * @param mixed $defaultValue Значение по умолчанию, если настройка не найдена
     * @param array $interp Массив значений для подстановки
     * @return array|bool|string|string[] Значение настройки
     */
    public static function siteParam(string|bool $key = false, mixed $defaultValue = null, array $interp = []): mixed
    {
        if (!self::$siteParams) {
            self::$siteParams = ModelSettings::values();
        }

        if ($key === false) {
            return self::$siteParams;
        }
        if (!array_key_exists($key, self::$siteParams)) {
            self::$siteParams[$key] = $defaultValue;
            if ($key !== true && $key !== '' && (is_scalar($defaultValue) || is_null($defaultValue))) {
                $type = match (true) {
                    is_int($defaultValue) => 'int',
                    is_bool($defaultValue) => 'bool',
                    default => 'string',
                };
                $value = is_bool($defaultValue) ? (int)$defaultValue : (string)$defaultValue;
                ModelSettings::insertStatic([
                    'name' => $key,
                    'alias' => $key,
                    'value' => $value,
                    'type' => $type,
                    'enum_values' => '',
                ]);
            }
        }
        $value = self::$siteParams[$key] ?? $defaultValue;
        if (is_string($value)) {
            $value = str_replace(
                array_map(fn($k) => '{' . $k . '}', array_keys($interp)),
                array_values($interp),
                $value,
            );
        }
        return $value;
    }

    public static function getComponent(): object
    {
        $route = (new Resolver())
            ->setMenuByLink(self::$menuByLink) // пункты меню с компонентом - тоже маршруты
            ->resolve(Container::getConfig()->getRoutes());

        // запоминаем маршрут, чтобы использовать его дальше при необходимости
        Container::getRequest()->route = $route;

        $componentClass = $route->getComponentClassName();
        return new $componentClass(...Injector::resolveClass($componentClass));
    }

    /**
     * @return string[]
     */
    public static function getExtensions(): array
    {
        $exts = array_slice(scandir(SF_ROOT_PATH . '/Extensions'), 2);
        return $exts;
    }

    public static function execute(): void
    {
        if (self::$contentOnly) {
            echo Page::content();
        } else {
            $config = Container::getConfig();
            if (isset($_REQUEST['print']) && is_file(SF_ROOT_PATH . '/theme/' . $config->theme . '/print.tpl')) {
                include SF_ROOT_PATH . '/theme/' . $config->theme . '/print.tpl';
                return;
            }
            if (is_file(SF_ROOT_PATH . '/theme/' . $config->theme . '/index.tpl')) {
                include SF_ROOT_PATH . '/theme/' . $config->theme . '/index.tpl';
            }
        }
    }

    public static function menu(string $type = 'tree'): array
    {
        switch ($type) {
            case 'by_id':
                return self::$menuById;
            case 'by_link':
                return self::$menuByLink;
            case 'by_ext':
                return self::$menuByExt;
        }
        return self::$menuTree;
    }

    public static function menuCurItem(): ?array
    {
        return self::$menuCur;
    }

    /**
     * Ставит статус 404 и возвращает HTML страницы ошибки
     */
    public static function error404(): string
    {
        $config = Container::getConfig();
        header('HTTP/1.0 404 Not Found');
        Page::seo('Error 404');
        if (is_file(SF_ROOT_PATH . '/theme/' . $config->theme . '/404.tpl')) {
            self::$contentOnly = true;
            return View::render(SF_ROOT_PATH . '/theme/' . $config->theme . '/404.tpl');
        }
        return (string)self::siteParam('error404');
    }

    public static function isHttps(): bool
    {
        return !empty($_SERVER['HTTPS']);
    }

    /**
     *
     * @param bool $withSlashDots [по умолчанию false] ://
     * @return string https / http
     */
    public static function httpProtocol(bool $withSlashDots = false): string
    {
        return (self::isHttps() ? 'https' : 'http') . ($withSlashDots ? '://' : '');
    }

    /**
     * Url опубликованной статики ядра (см. Assets::publish)
     * @return string например /cache/assets/1a2b3c4d5e6f7a8b
     */
    public static function webVendorPath(): string
    {
        return Assets::publish(SF_CORE_ROOT_PATH);
    }

    public static function vendorPath(): string|false
    {
        return realpath(__DIR__ . '/..');
    }
}

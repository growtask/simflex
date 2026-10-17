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
        // if request or response wasn't set, we still have to construct them
        // useful for backwards compatibility
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

        $q = 'SELECT alias, value FROM settings';
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$siteParams[$row['alias']] = $row['value'];
        }

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
     * Get setting value by alias
     *
     * Can interpolate values from array, example:
     * `value (k = 'greeting') = 'Hello {name}'`, then
     * `Core::siteParam('greeting', 'Hello {name}', ['name' => 'John'])` equals to `'Hello John'`
     *
     * If the setting is not found and $key is given, it is created in `settings` table
     * with $defaultValue (scalar or null), so it becomes editable in admin panel.
     *
     * @param string|bool $key false to get all settings, otherwise alias
     * @param mixed $defaultValue Default value if setting not found
     * @param array $interp Array of values to interpolate
     * @return array|bool|string|string[] Value of setting
     */
    public static function siteParam(string|bool $key = false, mixed $defaultValue = null, array $interp = []): mixed
    {
        if (!self::$siteParams) {
            $q = 'SELECT alias, value FROM settings';
            $rows = DB::assoc($q);
            foreach ($rows as $row) {
                self::$siteParams[$row['alias']] = $row['value'];
            }
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
            ->setMenuByLink(self::$menuByLink) // menu items with a component are routes too
            ->resolve(Container::getConfig()->getRoutes());

        // set route here so we can use it later, if needed
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
     * Sets the 404 status and returns the error page HTML
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
     * @param bool $withSlashDots [optional = false] ://
     * @return string https / http
     */
    public static function httpProtocol(bool $withSlashDots = false): string
    {
        return (self::isHttps() ? 'https' : 'http') . ($withSlashDots ? '://' : '');
    }

    /**
     * Url опубликованной статики ядра (см. Assets::publish)
     * @return string like /cache/assets/1a2b3c4d5e6f7a8b
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

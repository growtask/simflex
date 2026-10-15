<?php

namespace Simflex\Core;

use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DI\Injector;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Html\HtmlResponse;
use Simflex\Core\Page;
use Simflex\Core\Routing\Resolver;

class Core
{
    protected static bool $ajax = false;
    protected static array $uri = [];
    protected static string $path = '';
    protected static array $site_params = [];
    protected static array $menu_tree = [];
    protected static array $menu_by_id = [];
    protected static array $menu_by_link = [];
    protected static array $menu_by_ext = [];
    protected static ?array $menu_cur = null;
    protected static int $component_level = 0;
    protected static int $component_menu_id = 0;
    protected static bool $content_only = false;

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

        // TODO: get rid of deprecated code
        DB::bind(['SITE_PATH' => $request->getPath(), 'SITE_LINK' => $request->getPath()]);

        if ($request->isAjax()) {
            self::$ajax = true;
            self::$content_only = true;
            $sf_module_id = isset($_REQUEST['sf_module_id']) ? (int)$_REQUEST['sf_module_id'] : 0;
            if ($sf_module_id > 0) {
                Page::moduleById($sf_module_id);
                exit();
            }
        } else {
            $q = "SELECT title, description, keywords, metatags FROM seo WHERE link=@SITE_LINK";
            if ($row = DB::result($q)) {
                Page::seo($row['title'], $row['description'], $row['keywords'], true, $row['metatags']);
            }
        }

        $q = "SELECT alias, value FROM settings";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$site_params[$row['alias']] = $row['value'];
        }

        static::initMenu();
    }

    protected static function initMenu(): void
    {
        $q = "SELECT t1.*, t2.class
        FROM menu t1
        LEFT JOIN component t2 ON t2.component_id=t1.component_id
        WHERE t1.active=1
        ORDER BY t1.npp";

        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$menu_tree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            self::$menu_by_id[(int)$row['menu_id']] = $row;
            self::$menu_by_link[md5($row['link'])] = $row;
            self::$menu_by_ext[strtolower(substr($row['class'], 3))][] = $row;
            if ($row['link'] == self::$path && !self::$menu_cur) {
                self::$menu_cur = $row;
            }
        }
    }

    /**
     * @deprecated use Request::isAjax() instead
     */
    public static function ajax(): bool
    {
        return Container::getRequest()->isAjax();
    }

    /**
     * @deprecated use Request::getUrlParts() instead
     */
    public static function uri(int|false $i = false): array|string
    {
        $uri = Container::getRequest()->getUrlParts();

        if ($i === false) {
            return $uri;
        }

        $i += self::$component_level;
        return $uri[$i] ?? '';
    }

    /**
     * @deprecated use Request::getUrlParts() instead
     */
    public static function uri_r($i): string
    {
        $uri = Container::getRequest()->getUrlParts();

        $i = count($uri) - 2 - $i;;
        return $uri[$i] ?? '';
    }

    /**
     * @deprecated use Request::getPath() instead
     */
    public static function path(int $beg = 0, int $len = 0): string
    {
        $path = Container::getRequest()->getPath();
        $uri = self::uri();

        $beg = $beg < 0 ? count($uri) + $beg : $beg;
        if ($beg + $len > 0) {
            $path = $len ? join('/', array_slice($uri, $beg, $len)) : join('/', array_slice($uri, $beg));
            $path = str_replace('//', '/', '/' . $path . '/');
        }
        return $path ? $path : '/';
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
        if (!self::$site_params) {
            $q = "SELECT alias, value FROM settings";
            $rows = DB::assoc($q);
            foreach ($rows as $row) {
                self::$site_params[$row['alias']] = $row['value'];
            }
        }

        if ($key === false) {
            return self::$site_params;
        }
        if (!array_key_exists($key, self::$site_params)) {
            self::$site_params[$key] = $defaultValue;
            if ($key !== true && $key !== '' && (is_scalar($defaultValue) || is_null($defaultValue))) {
                $type = match (true) {
                    is_int($defaultValue) => 'int',
                    is_bool($defaultValue) => 'bool',
                    default => 'string',
                };
                $value = is_bool($defaultValue) ? (int)$defaultValue : (string)$defaultValue;
                DB::query(
                    "INSERT INTO settings (name, alias, value, type, enum_values) VALUES (?, ?, ?, ?, '')",
                    [$key, $key, $value, $type]
                );
            }
        }
        $value = self::$site_params[$key] ?? $defaultValue;
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
            ->setMenuByLink(self::$menu_by_link) // for deprecated routing by menu database table
            ->resolve(Container::getConfig()->getRoutes());

        // set route here so we can use it later, if needed
        Container::getRequest()->route = $route;

        $componentClass = $route->getComponentClassName();
        return new $componentClass(...Injector::resolveClass($componentClass));
    }

    public static function getComponentAPI(): object|false
    {
        $class = '';
        $path = '/';
        $i = 0;
        if (!empty(self::$menu_by_link[md5($path)]['class'])) {
            $class = self::$menu_by_link[md5($path)]['class'];
            self::$component_level = $i;
            self::$component_menu_id = self::$menu_by_link[md5($path)]['menu_id'];
        }
        $i++;
        foreach (self::uri() as $u) {
            if ($u) {
                $path .= $u . '/';
                if (!empty(self::$menu_by_link[md5($path)]['class'])) {
                    $class = self::$menu_by_link[md5($path)]['class'];
                    self::$component_level = $i;
                    self::$component_menu_id = self::$menu_by_link[md5($path)]['menu_id'];
                }
            }
            $i++;
        }

        if ($class) {
            $class = 'Api' . substr($class, 3);
            return new $class();
        } else {
            return false;
        }
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
        if (self::$content_only) {
            Page::content();
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
            case 'by_id' :
                return self::$menu_by_id;
            case 'by_link' :
                return self::$menu_by_link;
            case 'by_ext' :
                return self::$menu_by_ext;
        }
        return self::$menu_tree;
    }

    public static function menuCurItem(): ?array
    {
        return self::$menu_cur;
    }

    public static function componentPath(): string
    {
        return self::path(self::$component_level);
    }

    public static function componentLevel(): int
    {
        return self::$component_level;
    }

    public static function componentMenuID(): int
    {
        return self::$component_menu_id;
    }

    public static function componentTitle(): ?string
    {
        return self::$menu_by_id[self::$component_menu_id]['name'];
    }

    public static function error404(): void
    {
        $config = Container::getConfig();
        header("HTTP/1.0 404 Not Found");
        Page::seo('Error 404');
        if (is_file(SF_ROOT_PATH . '/theme/' . $config->theme . '/404.tpl')) {
            self::$content_only = true;
            include SF_ROOT_PATH . '/theme/' . $config->theme . '/404.tpl';
        } else {
            echo self::siteParam('error404');
        }
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

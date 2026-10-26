<?php

namespace Simflex\Admin;

use Simflex\Core\Container;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Html\HtmlResponse;
use Simflex\Core\Models\ModelSettings;
use Simflex\Core\Privilege;

class Core
{
    public static bool $ajax = false;
    public static bool $isAjax = false;
    private static array $uri = [];
    private static string $path = '';
    private static array $menuTree = [];
    private static array $menuById = [];
    private static array|false $menuCur = false;
    private static array $crumbs = [];
    private static array $siteParams = [];

    private function __construct()
    {

    }

    /**
     * Корень админки без завершающего слеша, задаётся в конфиге (adminPath)
     * @return string например /admin
     */
    public static function root(): string
    {
        $path = trim(Container::getConfig()->adminPath, '/');
        if ($path === '') {
            throw new \LogicException('Config adminPath must not be empty');
        }

        return '/' . $path;
    }

    /**
     * Адрес внутри админки
     * @param string $path Путь относительно корня админки, например 'content/'
     * @return string например /admin/content (без завершающего слеша)
     */
    public static function url(string $path = ''): string
    {
        $url = static::root() . '/' . ltrim($path, '/');
        $query = strpos($url, '?');
        $base = $query === false ? $url : substr($url, 0, $query);

        return rtrim($base, '/') . ($query === false ? '' : substr($url, $query));
    }

    /**
     * Относится ли путь запроса к админке
     */
    public static function isAdminPath(string $path): bool
    {
        return $path === static::root() || str_starts_with($path, static::root() . '/');
    }

    /**
     * Url опубликованной статики админки ядра (см. Assets::publish)
     * @return string например /cache/assets/1a2b3c4d5e6f7a8b/Admin
     */
    public static function webVendorPath(): string
    {
        return \Simflex\Core\Core::webVendorPath() . '/Admin';
    }

    public static function vendorPath(): string
    {
        return __DIR__;
    }

    public static function init(): void
    {

        if (!Container::isSet('request')) {
            Container::set('request', new HtmlRequest());
        }

        if (!Container::isSet('response')) {
            Container::set('response', new HtmlResponse());
        }

        $url_info = parse_url($_SERVER['REQUEST_URI']);
        self::$path = $url_info['path'];
        if (strlen(self::$path) > 1 && str_ends_with(self::$path, '/') && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            // адреса админки без завершающего слеша: /admin/content/ => /admin/content
            $query = isset($url_info['query']) ? '?' . $url_info['query'] : '';
            header('Location: ' . rtrim(self::$path, '/') . $query, true, 301);
            exit;
        }
        self::$path = rtrim(self::$path, '/');
        // сегменты пути относительно корня админки: /admin/content/ => ['content', '']
        self::$uri = explode('/', ltrim(substr(self::$path, strlen(static::root())), '/'));

        // ajax: в ответе только содержимое раздела, без макета админки
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest' || !empty($_GET['ajax'])) {
            self::$ajax = true;
            self::$isAjax = true;
        }

        if (static::uri(0) == 'login') {
            Auth::login(
                $_REQUEST['login']['login'] ?? null,
                $_REQUEST['login']['password'] ?? null,
                isset($_REQUEST['login']['remember']),
                $_REQUEST['r'] ?? static::url()
            );
        } elseif (static::uri(0) == 'logout') {
            Auth::logout();
        }

        $user = Container::getUser();
        if (!$user || !$user->ican(Privilege::ADMIN)) {
            return;
        }

        foreach (Menu::getItems() as $key => $item) {
            if (!$user->ican($item->privilege)) {
                continue;
            }
            $row = [
                'id' => $key,
                'pid' => Menu::getParent($key),
                // ссылки объявлены относительно корня админки
                'link' => str_starts_with($item->link, '/') ? static::url($item->link) : rtrim($item->link, '/'),
                'name' => $item->name,
                'handler' => $item->handler,
                'icon' => $item->icon,
                'hidden' => $item->hidden,
            ];
            self::$menuTree[$row['pid']][$key] = $row;
            self::$menuById[$key] = $row;
            if ($row['link'] == self::$path) {
                self::$menuCur = $row;
            }
        }

        if (!self::$isAjax) {
            if (self::$menuCur) {
                self::crumbsSet(self::$menuCur);
            }
        }

        self::$siteParams = ModelSettings::values();
    }

    public static function ajax(): bool
    {
        return self::$ajax;
    }

    /**
     * Сегмент пути относительно корня админки: для /admin/content/ uri(0) = 'content'
     */
    public static function uri(int $i = 0): string
    {
        return self::$uri[$i] ?? '';
    }

    /**
     * Пункты меню, доступные пользователю: id родителя ('' для корня) => id => запись
     * (id, pid, link, name, handler, icon, hidden).
     * id — ссылка пункта, как она объявлена в Menu
     */
    public static function menu(): array
    {
        return self::$menuTree;
    }

    public static function menuCurItem(string|false $field = false): mixed
    {
        return $field ? @self::$menuCur[$field] : self::$menuCur;
    }

    private static function crumbsSet(array $item = []): void
    {
        if (isset(self::$menuById[$item['pid']])) {
            self::crumbsSet(self::$menuById[$item['pid']]);
        }
        self::$crumbs[] = ['link' => $item['link'], 'name' => $item['name']];
    }

    public static function crumbs(): array
    {
        return self::$crumbs;
    }

    public static function path(): string
    {
        return self::$path;
    }

    public static function execute(): void
    {
        $user = Container::getUser();
        if ($user && !$user->ican(Privilege::ADMIN)) {
            http_response_code(403);
            include 'theme/tpl/denied.tpl';
        } elseif ($user) {
            if (self::ajax()) {
                echo Page::content();
            } else {
                include 'theme/tpl/index.tpl';
            }
        } else {
            $back = $_SERVER['REQUEST_URI'];
            if (str_contains($back, 'logout')) {
                $back = static::url();
            }
            include 'theme/tpl/login.tpl';
        }
    }

    public static function siteParam($key): mixed
    {
        return self::$siteParams[$key] ?? '';
    }
}

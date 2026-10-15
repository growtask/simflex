<?php

namespace Simflex\Admin;


use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Html\HtmlResponse;
use Simflex\Admin\Auth;
use Simflex\Admin\Page;

class Core
{

    public static bool $ajax = false;
    public static bool $isAjax = false;
    private static array $uri = [];
    private static string $path = '';
    private static array $menuTree = [];
    private static array $menuById = [];
    private static array $menuByLink = [];
    private static array|false $menuCur = false;
    private static array $crumbs = [];
    private static array $siteParams = [];

    private function __construct()
    {

    }

    /**
     * Корень админки без завершающего слеша, задаётся в конфиге (adminPath)
     * @return string like /admin
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
     * @return string like /admin/content/
     */
    public static function url(string $path = ''): string
    {
        return static::root() . '/' . ltrim($path, '/');
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
     * @return string like /cache/assets/1a2b3c4d5e6f7a8b/Admin
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
        // сегменты пути относительно корня админки: /admin/content/ => ['content', '']
        self::$uri = explode('/', ltrim(substr(self::$path, strlen(static::root())), '/'));

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
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
        if (!$user || !$user->ican(Auth::LOGIN_PRIV)) {
            return;
        }

        $privIds = array_map('intval', $user->getPrivileges()['ids'] ?? []) ?: [0];
        $q = "
            SELECT menu_id, menu_pid, link, name, model, icon, hidden
            FROM admin_menu
            WHERE priv_id IN(" . join(',', $privIds) . ")
            ORDER BY npp, menu_id
        ";
        $rows = DB::assoc($q) ?: [];
        foreach ($rows as $row) {
            // ссылки хранятся относительно корня админки
            if (str_starts_with($row['link'], '/')) {
                $row['link'] = static::url($row['link']);
            }
            self::$menuTree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            self::$menuById[(int)$row['menu_id']] = $row;
            self::$menuByLink[md5($row['link'])] = $row;
            if ($row['link'] == self::$path) {
                self::$menuCur = $row;
            }
        }

        if (!self::$isAjax) {
            if (self::$menuCur) {
                self::crumbsSet(self::$menuCur);
            }
        }

        $q = "SELECT alias, value FROM settings";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$siteParams[$row['alias']] = $row['value'];
        }


        DB::bind(['SITE_PATH' => self::$path]);
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
        if (isset(self::$menuById[(int)$item['menu_pid']])) {
            self::crumbsSet(self::$menuById[(int)$item['menu_pid']]);
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
        if ($user && !$user->ican(Auth::LOGIN_PRIV)) {
            http_response_code(403);
            include 'theme/tpl/denied.tpl';
        } elseif ($user) {
            if (self::ajax()) {
                Page::content();
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

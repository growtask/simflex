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
    private static array $menu_tree = [];
    private static array $menu_by_id = [];
    private static array $menu_by_link = [];
    private static array|false $menu_cur = false;
    private static array $crumbs = [];
    private static array $site_params = [];

    private function __construct()
    {

    }

    /**
     * @return string like /vendor/growtask/simflex/src/Admin
     */
    public static function webVendorPath(): string
    {
        return '/vendor/growtask/simflex/src/Admin';
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
        self::$uri = array_slice(explode('/', self::$path), 1);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            self::$ajax = true;
            self::$isAjax = true;
        }

        if (static::uri(1) == 'login') {
            Auth::login(
                $_REQUEST['login']['login'] ?? null,
                $_REQUEST['login']['password'] ?? null,
                isset($_REQUEST['login']['remember']),
                $_REQUEST['r'] ?? '/admin/'
            );
        } elseif (static::uri(1) == 'logout') {
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
            self::$menu_tree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            self::$menu_by_id[(int)$row['menu_id']] = $row;
            self::$menu_by_link[md5($row['link'])] = $row;
            if ($row['link'] == self::$path) {
                self::$menu_cur = $row;
            }
        }

        if (!self::$isAjax) {
            if (self::$menu_cur) {
                self::crumbsSet(self::$menu_cur);
            }
        }

        $q = "SELECT alias, value FROM settings";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$site_params[$row['alias']] = $row['value'];
        }


        DB::bind(['SITE_PATH' => self::$path]);
    }

    public static function ajax(): bool
    {
        return self::$ajax;
    }

    public static function uri(int $i = 0): string
    {
        return isset(self::$uri[$i]) ? self::$uri[$i] : '';
    }

    public static function menu(): array
    {
        return self::$menu_tree;
    }

    public static function menuCurItem(string|false $field = false): mixed
    {
        return $field ? @self::$menu_cur[$field] : self::$menu_cur;
    }

    private static function crumbsSet(array $item = []): void
    {
        if (isset(self::$menu_by_id[(int)$item['menu_pid']])) {
            self::crumbsSet(self::$menu_by_id[(int)$item['menu_pid']]);
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
            if (strpos($back, 'logout') !== false) {
                $back = '/admin/';
            }
            include 'theme/tpl/login.tpl';
        }
    }

    public static function siteParam($key): mixed
    {
        return self::$site_params[$key] ?? '';
    }

}

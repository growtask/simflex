<?php

namespace Simflex\Admin;

use Simflex\Admin\Notify\Collection;
use Simflex\Admin\Handlers\Group;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Privilege;
use Simflex\Core\Request;

class Page
{
    protected static string $menu = '';
    protected static string $content = '';
    protected static array $positions = [];
    protected static array $modules = [];
    protected static array $css = [];
    protected static array $cssCheck = [];
    protected static array $js = [];
    protected static array $jsCheck = [];

    protected static Handler|false $handler = false;

    public static function init(): void
    {

        if (!Container::getUser()?->ican(Privilege::ADMIN)) {
            return;
        }

        if ($widgetId = (int)@$_GET['sfwidget']) {
            $q = "SELECT * FROM admin_widget WHERE active = 1 AND widget_id = $widgetId";
            $row = DB::result($q);
            $class = $row['class'];
            $widget = new $class($row);
            self::$content = $widget->execute();
            return;
        }

        if (!Container::getRequest()) {
            Container::set('request', new Request());
        }

        self::$handler = static::handler((string)Core::menuCurItem('handler'));

        self::$content = self::$handler->content();

        if (!Core::$isAjax) {
            foreach (AdminModules::getForSection((string)Core::menuCurItem('id')) as $module) {
                $mod = new ($module->class)([
                    'item_id' => 0,
                    'class' => $module->class,
                    'name' => $module->name,
                    'posname' => $module->position,
                    'params' => serialize($module->params),
                ]);
                self::$positions[$module->position][] = $mod->execute();
            }
        }
    }

    /**
     * Обработчик текущего раздела: указанный в его пункте меню, по умолчанию страница группы
     * (подпункты, виджеты)
     */
    protected static function handler(string $handler): Handler
    {
        return is_subclass_of($handler, Handler::class) ? new $handler() : new Group();
    }

    public static function content(): string
    {
        return self::$content;
    }

    public static function position(string $posname): string
    {
        if (!$posname || empty(self::$positions[$posname])) {
            return '';
        }
        return implode('<hr class="module-separator" />', array_filter(self::$positions[$posname], 'strlen'));
    }

    public static function module(string $modname): string
    {
        return $modname ? (self::$modules[$modname] ?? '') : '';
    }

    public static function css(string $file, int $idx = 100): void
    {
        $idx = (int)$idx;
        if (empty(self::$css[$idx][md5($file)])) {
            if (isset(self::$cssCheck[md5($file)])) {
                unset(self::$css[self::$cssCheck[md5($file)]][md5($file)]);
                unset(self::$cssCheck[md5($file)]);
            }
            self::$cssCheck[md5($file)] = $idx;
            self::$css[$idx][md5($file)] = $file;
        }
    }

    public static function js(string $file, int $idx = 100): void
    {
        $idx = (int)$idx;
        if (empty(self::$js[$idx][md5($file)])) {
            if (isset(self::$jsCheck[md5($file)])) {
                unset(self::$js[self::$jsCheck[md5($file)]][md5($file)]);
                unset(self::$jsCheck[md5($file)]);
            }
            self::$jsCheck[md5($file)] = $idx;
            self::$js[$idx][md5($file)] = $file;
        }
    }

    public static function coreCss(string $file, int $idx = 100): void
    {
        $file = Core::webVendorPath() . $file;
        static::css($file, $idx);
    }

    public static function coreJs(string $file, int $idx = 100): void
    {
        $file = Core::webVendorPath() . $file;
        static::js($file, $idx);
    }

    public static function meta(): string
    {
        $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\r\n";

        ksort(self::$css);
        foreach (self::$css as $css_arr) {
            foreach ($css_arr as $css) {
                $html .= '<link type="text/css" rel="stylesheet" href="' . $css . '" />' . "\r\n";
            }
        }
        ksort(self::$js);
        foreach (self::$js as $js_arr) {
            foreach ($js_arr as $js) {
                $html .= '<script type="text/javascript" src="' . $js . '"></script>' . "\r\n";
            }
        }

        if (Container::getConfig()->devMode) {
            $html .= Container::get('debugbar')->renderHead();
        }
        return $html;
    }

    /**
     * @incomplete Подсистема Notify пока не подключена к админке; она будет доработана и использована позже.
     */
    public static function notifications(): string
    {
        return Collection::getInstance()->content();
    }
}

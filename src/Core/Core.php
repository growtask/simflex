<?php

namespace Simflex\Core;

use Simflex\Core\DI\Injector;
use Simflex\DB\DB;
use Simflex\Http\ComponentBase;
use Simflex\Http\Html\HtmlRequest;
use Simflex\Http\Html\HtmlResponse;
use Simflex\Http\Routing\Resolver;

/**
 * Ядро сайта: обработка запроса к сайту (не к админке).
 *
 * init() - запрос и ответ в контейнере, SEO страницы из раздела «SEO», ajax-запрос модуля (sf_module_id);
 * getComponent() - компонент страницы по маршрутам и меню сайта; execute() - вывод темы сайта.
 * Настройки сайта - SiteSettings, меню - SiteMenu, состав страницы - Page
 */
class Core
{
    /** Выводить только содержимое компонента, без темы (ajax-запрос) */
    protected static bool $contentOnly = false;

    private function __construct()
    {
    }

    public static function init(): void
    {
        if (!Container::isSet('request')) {
            Container::set('request', new HtmlRequest());
        }
        if (!Container::isSet('response')) {
            Container::set('response', new HtmlResponse());
        }

        $request = Container::getRequest();
        if (!$request->isAjax()) {
            $row = DB::result('SELECT title, description, keywords, metatags FROM seo WHERE link = ?', '', [$request->getPath()]);
            if ($row) {
                PageSeo::seo($row['title'], $row['description'], $row['keywords'], true, $row['metatags']);
            }
            return;
        }

        static::$contentOnly = true;
        $moduleId = (int)$request->request('sf_module_id', 0);
        if ($moduleId > 0) {
            echo Page::moduleById($moduleId);
            exit;
        }
    }

    /**
     * Компонент страницы: маршрут из provider/routes.php (ядра, расширений, сайта) или пункт меню сайта
     * с компонентом. Маршрут запоминается в Request::$route
     */
    public static function getComponent(): ComponentBase
    {
        $menu = array_filter(SiteMenu::getByLink(), fn(array $item) => !empty($item['class']));
        $route = (new Resolver())
            ->setMenu($menu)
            ->resolve(Container::getConfig()->getRoutes());

        Container::getRequest()->route = $route;

        $class = $route->getComponentClassName();
        return new $class(...Injector::resolveClass($class));
    }

    /**
     * Выводит страницу: тему сайта (theme/<тема>/index.tpl, print.tpl для ?print) или, для ajax-запроса,
     * только содержимое компонента
     */
    public static function execute(): void
    {
        if (static::$contentOnly) {
            echo Page::content();
            return;
        }

        $dir = SF_ROOT_PATH . '/theme/' . Container::getConfig()->theme;
        if (Container::getRequest()->get('print') !== null && is_file($dir . '/print.tpl')) {
            include $dir . '/print.tpl';
            return;
        }
        if (is_file($dir . '/index.tpl')) {
            include $dir . '/index.tpl';
        }
    }
}

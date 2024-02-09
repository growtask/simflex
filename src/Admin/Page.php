<?php

namespace Simflex\Admin;


use Simflex\Admin\Base;
use Simflex\Admin\Base\Component;
use Simflex\Admin\Base\ModuleItem;
use Simflex\Admin\Base\Struct;
use Simflex\Admin\Core;
use Simflex\Admin\Modules\Account\Account;
use Simflex\Admin\Notify\Collection;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Request;
use Simflex\Core\User;

class Page
{

    protected static $menu = '';
    protected static $content = '';
    protected static $positions = array();
    protected static $modules = array();
    protected static $css = array();
    protected static $css_check = array();
    protected static $js = array();
    protected static $js_check = array();

    /**
     *
     * @var Base
     */
    protected static $driver = false;

    public static function init()
    {

        if (!User::ican('simplex_admin')) {
            return;
        }

        $notify = Collection::getInstance();
        if (isset($_GET['sfnotify'])) {
            self::$content = $notify->ajaxData();
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

        self::$driver = false;

        $menuCurModel = Core::menuCurItem('model');
        $tableData = null;
        if ($menuCurModel) {
            $q = "SELECT * FROM struct_table WHERE name = '$menuCurModel'";
            $tableData = DB::result($q);
        }
        $extDriverClass = $tableData['class'] ?? null;

        if (class_exists($extDriverClass)) {
            self::$driver = new $extDriverClass();
        }

        if (!Container::getRequest()) {
            Container::set('request', new Request());
        }

        if (!self::$driver) {
            // todo: lol fix this
            if (Container::getRequest()->getPath() == '/admin/account/') {
                self::$driver = new Account([]);
            } elseif (in_array($menuCurModel, array('struct_param', 'struct_data', 'module_param', 'struct_table', 'content_template_param'))) {
                self::$driver = new Struct();
            } elseif (in_array($menuCurModel, array('module_item'))) {
                self::$driver = new ModuleItem();
            } elseif (in_array($menuCurModel, array('component'))) {
                self::$driver = new Component();
            } else {
                self::$driver = new Base();
            }
        }

        self::$content = self::$driver->execute();

        if (!Core::$isAjax) {
            $curmenu = Core::menuCurItem();
            $q = "
                SELECT t1.item_id, t1.module_id, t1.menu_id, t1.name, 0 is_title, t1.posname, t2.class, t1.params
                FROM module_item t1
                JOIN module t2 using(module_id)
                WHERE t1.active = 1 and t2.type = 'admin'
                ORDER BY t1.npp, t1.item_id
            ";
            $rows = DB::assoc($q);
            foreach ($rows as $row) {
                if (empty($row['menu_id']) || (int)$row['menu_id'] == $curmenu['menu_id']) {
                    $class = $row['class'];
                    if (strpos($class, '\\') === false) {
                        $class = "Simflex\Admin\Modules\\$class\\$class";
                    }
                    $mod = new $class($row);
                    self::$positions[$row['posname']][] = $mod->execute();
                }
            }
        }
    }

    public static function content()
    {
        echo self::$content;
    }

    public static function position($posname)
    {
        if ($posname && !empty(self::$positions[$posname])) {
            $i = 0;
            foreach (self::$positions[$posname] as $mod_content) {
                if ($mod_content) {
                    if ($i) {
                        echo '<hr class="module-separator" />';
                    }
                    echo $mod_content;
                    $i++;
                }
            }
        }
    }

    public static function module($modname)
    {
        if ($modname && !empty(self::$modules[$modname])) {
            echo self::$modules[$modname];
        }
    }

    public static function css($file, $idx = 100)
    {
        $idx = (int)$idx;
        if (empty(self::$css[$idx][md5($file)])) {
            if (isset(self::$css_check[md5($file)])) {
                unset(self::$css[self::$css_check[md5($file)]][md5($file)]);
                unset(self::$css_check[md5($file)]);
            }
            self::$css_check[md5($file)] = $idx;
            self::$css[$idx][md5($file)] = $file;
        }
    }

    public static function js($file, $idx = 100)
    {
        $idx = (int)$idx;
        if (empty(self::$js[$idx][md5($file)])) {
            if (isset(self::$js_check[md5($file)])) {
                unset(self::$js[self::$js_check[md5($file)]][md5($file)]);
                unset(self::$js_check[md5($file)]);
            }
            self::$js_check[md5($file)] = $idx;
            self::$js[$idx][md5($file)] = $file;
        }
    }

    public static function coreCss($file, $idx = 100)
    {
        $file = Core::webVendorPath() . $file;
        static::css($file, $idx);
    }

    public static function coreJs($file, $idx = 100)
    {
        $file = Core::webVendorPath() . $file;
        static::js($file, $idx);
    }

    public static function meta()
    {
        //echo $this->description ? '<meta name="description" content="'.$this->description.'" />'."\r\n" : '';
        //echo $this->keywords ? '<meta name="keywords" content="'.$this->keywords.'" />'."\r\n" : '';

        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', "\r\n";

        ksort(self::$css);
        foreach (self::$css as $css_arr) {
            foreach ($css_arr as $css) {
                echo '<link type="text/css" rel="stylesheet" href="', $css, '" />', "\r\n";
            }
        }
        ksort(self::$js);
        foreach (self::$js as $js_arr) {
            foreach ($js_arr as $js) {
                echo '<script type="text/javascript" src="', $js, '"></script>', "\r\n";
            }
        }
    }

    public static function notifications()
    {
        Collection::getInstance()->content();
    }

}

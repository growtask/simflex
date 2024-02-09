<?php

namespace Simflex\Extensions\Breadcrumbs;

use Simflex\Core\Core;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends ModuleBase
{
    protected static $arr = array();
    protected $crumbs = array();
    protected static $skip = [];

    protected function content()
    {
        $arr = array();
        $menu = Core::menu('by_id');
        $id = Core::componentMenuID();
        $curmenu = Core::menuCurItem();
        if (isset($menu[$curmenu['menu_id']])) {
            $id = $curmenu['menu_id'];
        }
        $is_main = false;
        while (isset($menu[$id])) {
            $arr[md5($menu[$id]['link'])] = array('name' => $menu[$id]['name'], 'link' => $menu[$id]['link']);
            if ($menu[$id]['link'] == '/') {
                $is_main = true;
            }
            $id = $menu[$id]['menu_pid'];
        }
        if (!$is_main) {
            $arr[md5('/')] = array('name' => 'Главная', 'link' => '/');
        }
        $this->crumbs = array_reverse($arr);

        $arr = array_reverse(self::$arr);
        $this->crumbs = array_merge($this->crumbs, $arr);

        $cnt = count($this->crumbs);
        if ($cnt > 1) {
            $crumbs = array();
            $i = 0;
            foreach ($this->crumbs as $crumb) {
                $crumbs[] = $i + 1 < $cnt ? '<a href="' . $crumb['link'] . '">' . $crumb['name'] . '</a>' : '<span>' . $crumb['name'] . '</span>';
                $i++;
            }
            include dirname(__FILE__) . '/breadcrumbs.tpl';
        }
    }

    public static function get(): array
    {
        $arr = array_reverse(self::$arr);
        foreach (self::$skip as $s) {
            unset($arr[$s]);
        }

        return $arr;
    }

    public static function getLast(): array
    {
        return self::$arr[array_key_first(self::$arr)] ?? [];
    }

    public static function remove($link)
    {
        self::$skip[] = md5($link);
    }

    public static function add($name, $link)
    {
        if (!isset(self::$arr[md5($link)])) {
            self::$arr[md5($link)] = ['name' => $name, 'link' => $link];
        }
    }
}

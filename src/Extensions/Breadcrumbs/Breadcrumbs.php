<?php

namespace Simflex\Extensions\Breadcrumbs;

use Simflex\Core\Core;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends ModuleBase
{
    protected static array $arr = [];
    protected array $crumbs = [];
    protected static array $skip = [];

    protected function content(): string
    {
        $arr = [];
        $menu = Core::menu('by_id');
        $id = Core::componentMenuID();
        $curmenu = Core::menuCurItem();
        if (isset($menu[$curmenu['menu_id']])) {
            $id = $curmenu['menu_id'];
        }
        $is_main = false;
        while (isset($menu[$id])) {
            $arr[md5($menu[$id]['link'])] = ['name' => $menu[$id]['name'], 'link' => $menu[$id]['link']];
            if ($menu[$id]['link'] == '/') {
                $is_main = true;
            }
            $id = $menu[$id]['menu_pid'];
        }
        if (!$is_main) {
            $arr[md5('/')] = ['name' => 'Главная', 'link' => '/'];
        }
        $this->crumbs = array_reverse($arr);

        $arr = array_reverse(self::$arr);
        $this->crumbs = array_merge($this->crumbs, $arr);

        $cnt = count($this->crumbs);
        if ($cnt > 1) {
            $crumbs = [];
            $i = 0;
            foreach ($this->crumbs as $crumb) {
                $crumbs[] = $i + 1 < $cnt ? '<a href="' . $crumb['link'] . '">' . $crumb['name'] . '</a>' : '<span>' . $crumb['name'] . '</span>';
                $i++;
            }
            return $this->render(__DIR__ . '/breadcrumbs.tpl', ['crumbs' => $crumbs]);
        }
        return '';
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

    public static function remove($link): void
    {
        self::$skip[] = md5($link);
    }

    public static function add($name, $link): void
    {
        if (!isset(self::$arr[md5($link)])) {
            self::$arr[md5($link)] = ['name' => $name, 'link' => $link];
        }
    }
}

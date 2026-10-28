<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Core;
use Simflex\Admin\Handler;
use Simflex\Core\Container;
use Simflex\DB\DB;

/**
 * Страница группы меню со списком подпунктов; главная страница админки показывает ещё и виджеты.
 * Раздел пункта меню без обработчика и адреса, которого нет в меню
 */
class Group extends Handler
{
    public function content(): string
    {
        return $this->widgets() . $this->subitems();
    }

    protected function subitems(): string
    {
        $cur_id = Core::menuCurItem('id') ?? '';
        $menu = Core::menu();
        if (!isset($menu[$cur_id])) {
            return '';
        }
        return $this->render(__DIR__ . '/tpl/group.tpl', get_defined_vars());
    }

    protected function widgets(): string
    {
        $q = 'SELECT * FROM admin_widget WHERE active = 1 ORDER BY npp, widget_id';
        $rows = DB::assoc($q) ?: [];
        $html = '';
        foreach ($rows as $row) {
            if (!$row['privilege'] || Container::getUser()?->ican($row['privilege'])) {
                $class = $row['class'];
                $widget = new $class($row);
                $html .= $widget->execute();
            }
        }
        return $html === '' ? '' : '<div class="widgets">' . $html . '</div>';
    }
}

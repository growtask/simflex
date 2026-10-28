<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Models\AdminWidget;
use Simflex\Core\Admin\Page;
use Simflex\Core\Admin\Handler;
use Simflex\Core\System\Container;

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
        $cur_id = Page::get()->getCurrentMenuItem('id') ?? '';
        $menu = Page::get()->getMenu();
        if (!isset($menu[$cur_id])) {
            return '';
        }
        return $this->render(__DIR__ . '/tpl/group.tpl', get_defined_vars());
    }

    protected function widgets(): string
    {
        $rows = AdminWidget::findAdv()->asArray()->where(['active' => 1])->orderBy('npp, widget_id')->all();
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

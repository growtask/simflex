<?php

namespace Simflex\Core\Admin\Modules\Menu;

use Simflex\Core\Admin\Page;
use Simflex\Core\System\ModuleBase;

class Menu extends ModuleBase
{
    protected string $name = 'menu';

    public function content(): string
    {
        $menu = Page::get()->getMenu();
        return $this->render(__DIR__ . '/tpl/menu.tpl', ['menu' => $menu]);
    }
}

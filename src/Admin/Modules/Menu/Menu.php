<?php

namespace Simflex\Admin\Modules\Menu;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;

class Menu extends ModuleBase
{
    protected string $name = 'menu';

    public function content(): string
    {
        $menu = Container::getCore()::menu();
        return $this->render(__DIR__ . '/tpl/menu.tpl', ['menu' => $menu]);
    }
}

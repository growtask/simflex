<?php

namespace Simflex\Admin\Modules\Menu;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;

class Menu extends ModuleBase
{

    protected $name = 'menu';

    public function content()
    {
        $menu = Container::getCore()::menu();
        include dirname(__FILE__) . '/tpl/menu.tpl';
    }

}

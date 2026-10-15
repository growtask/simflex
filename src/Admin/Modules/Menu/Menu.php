<?php

namespace Simflex\Admin\Modules\Menu;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;
use Simflex\Core\ModuleType;

class Menu extends ModuleBase
{
    public static function extensionTitle(): string
    {
        return 'Admin - Меню';
    }

    public static function moduleType(): ModuleType
    {
        return ModuleType::Admin;
    }

    protected string $name = 'menu';

    public function content(): void
    {
        $menu = Container::getCore()::menu();
        include dirname(__FILE__) . '/tpl/menu.tpl';
    }
}

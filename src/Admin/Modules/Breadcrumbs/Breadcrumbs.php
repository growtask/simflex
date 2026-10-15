<?php

namespace Simflex\Admin\Modules\Breadcrumbs;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;
use Simflex\Core\ModuleType;

class Breadcrumbs extends ModuleBase
{
    public static function extensionTitle(): string
    {
        return 'Admin - Хлебные крошки';
    }

    public static function moduleType(): ModuleType
    {
        return ModuleType::Admin;
    }

    protected string $name = 'breadcrumbs';

    public function content(): string
    {
        $crumbs = Container::getCore()::crumbs();
        return $this->render(__DIR__ . '/tpl/crumbs.tpl', ['crumbs' => $crumbs]);
    }
}

<?php

namespace Simflex\Admin\Modules\Breadcrumbs;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends ModuleBase
{
    protected string $name = 'breadcrumbs';

    public function content(): string
    {
        $crumbs = Container::getCore()::crumbs();
        return $this->render(__DIR__ . '/tpl/crumbs.tpl', ['crumbs' => $crumbs]);
    }
}

<?php

namespace Simflex\Admin\Modules\Breadcrumbs;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends ModuleBase
{
    protected string $name = 'breadcrumbs';

    public function content(): void
    {
        $crumbs = Container::getCore()::crumbs();
        include 'tpl/crumbs.tpl';
    }
}

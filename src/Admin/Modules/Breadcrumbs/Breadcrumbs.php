<?php

namespace Simflex\Core\Admin\Modules\Breadcrumbs;

use Simflex\Core\Admin\Page;
use Simflex\Core\System\ModuleBase;

class Breadcrumbs extends ModuleBase
{
    protected string $name = 'breadcrumbs';

    public function content(): string
    {
        $crumbs = Page::get()->getCrumbs();
        return $this->render(__DIR__ . '/tpl/crumbs.tpl', ['crumbs' => $crumbs]);
    }
}

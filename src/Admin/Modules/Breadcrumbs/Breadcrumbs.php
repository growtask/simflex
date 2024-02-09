<?php

namespace Simflex\Admin\Modules\Breadcrumbs;

use Simflex\Core\Container;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends ModuleBase
{

    protected $name = 'breadcrumbs';

    public function content()
    {
        $crumbs = Container::getCore()::crumbs();
        include 'tpl/crumbs.tpl';
//
//        if (count($crumbs) > 0) {
//            $links = array();
//            $cnt = count($crumbs);
//            foreach ($crumbs as $i => $row) {
//                $links[] = '<a href="' . $row['link'] . '">' . $row['name'] . '</a>';
//            }
//            include dirname(__FILE__) . '/tpl/crumbs.tpl';
//        }
    }

}

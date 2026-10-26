<?php

namespace Simflex\Admin;

/**
 * Module placed on the admin panel pages, see AdminModules::init()
 */
readonly class AdminModule
{
    /**
     * @param class-string<\Simflex\Core\ModuleBase> $class
     * @param string $position Position in the admin panel layout: Page::position($position)
     * @param string $name Title of the module, shown when $params['is_title'] is set
     * @param array $params Module params, as the site modules keep in module_item.params
     * @param string[] $sections Links of the menu items (as declared in Menu) the module is shown on.
     *        Empty — on every page: the menu, the breadcrumbs
     */
    public function __construct(
        public string $class,
        public string $position,
        public string $name = '',
        public array $params = [],
        public array $sections = [],
    ) {
    }
}

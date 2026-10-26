<?php

namespace Simflex\Admin\Structure;

/**
 * Admin section declared in code, named by a menu item (MenuItem::$handler).
 * A table section extends Table; a section without a table names its class only
 */
abstract class Handler
{
    /**
     * @param string $class Class doing the section: \Simflex\Admin\Base subclass or a module (\Simflex\Core\ModuleBase).
     *        A table section without it gets the generic \Simflex\Admin\Base, e.g.
     *        \Simflex\Extensions\Content\Admin\AdminContent::class for non-generic behaviour
     */
    public function __construct(
        public readonly string $class = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'class' => $this->class,
        ];
    }
}

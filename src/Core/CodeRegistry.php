<?php

namespace Simflex\Core;

/**
 * Registry declared in code: the core declares its entries in init(), the app extends them in a subclass
 * named in the config (configProperty()): init() calling parent::init() and adding its own.
 * The core calls the base class and gets the app's registry
 */
abstract class CodeRegistry
{
    /**
     * Declares the entries. The app's subclass calls parent::init() and adds or changes its own
     */
    abstract protected static function init(): void;

    /**
     * Property of the config naming the app's subclass
     */
    abstract protected static function configProperty(): string;

    /**
     * Class declaring the entries: the app's subclass from the config, otherwise this one
     *
     * @return class-string<static>
     */
    public static function registryClass(): string
    {
        $class = Container::isSet('config') ? (Container::getConfig()->{static::configProperty()} ?? '') : '';
        return $class && is_a($class, static::class, true) ? $class : static::class;
    }
}

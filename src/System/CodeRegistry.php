<?php

namespace Simflex\Core\System;

/**
 * Реестр, объявленный в коде: ядро объявляет свои записи в init(), приложение расширяет их в подклассе,
 * указанном в конфиге (configProperty()): init() вызывает parent::init() и добавляет свои.
 * Ядро обращается к базовому классу и получает реестр приложения
 */
abstract class CodeRegistry
{
    /**
     * Объявляет записи. Подкласс приложения вызывает parent::init() и добавляет или меняет свои
     */
    abstract protected static function init(): void;

    /**
     * Свойство конфига с именем подкласса приложения
     */
    abstract protected static function configProperty(): string;

    /**
     * Класс, объявляющий записи: подкласс приложения из конфига, иначе этот
     *
     * @return class-string<static>
     */
    public static function registryClass(): string
    {
        $class = Container::isSet('config') ? (Container::getConfig()->{static::configProperty()} ?? '') : '';
        return $class && is_a($class, static::class, true) ? $class : static::class;
    }
}

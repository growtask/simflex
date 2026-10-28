<?php

namespace Simflex\Core\System;

/**
 * Где выполняется код: на сайте, в админке, в API или в консоли.
 *
 * Задаёт Core::init() при загрузке, текущее значение - Container::getLocation(). Код ядра, расширений и сайта
 * может на него полагаться: до загрузки сайта код не выполняется (unit-тесты задают окружение в bootstrap)
 */
enum Location: string
{
    case Site = 'site';
    case Admin = 'admin';
    case Api = 'api';
    case Cli = 'cli';

    /**
     * Веб-запрос: сайт, админка или API
     */
    public function isWeb(): bool
    {
        return $this !== self::Cli;
    }
}

<?php

/*
 * Функции ядра, подключаются автозагрузчиком composer (autoload.files)
 */

use Composer\InstalledVersions;
use Simflex\Core\Buffer;

/**
 * Значение переменной окружения
 */
function env(string $name, mixed $defaultValue = null): mixed
{
    return array_key_exists($name, $_ENV) ? $_ENV[$name] : $defaultValue;
}

/**
 * Путь к статике
 * @param string $path Относительный путь к файлу статики
 * @param bool $siteOnly true - только статика сайта
 * @return string Полный путь к файлу статики, с mtime файла в качестве версии (?v=...): после изменения
 *                файла браузер не держит устаревшую копию
 */
function asset(string $path, bool $siteOnly = false): string
{
    // SF_LOCATION объявляет точка входа (public/index.php, console.php)
    if (!$siteOnly && defined('SF_LOCATION') && SF_LOCATION == SF_LOCATION_ADMIN) {
        $url = \Simflex\Admin\Core::webVendorPath() . '/theme/' . $path;
        $file = \Simflex\Admin\Core::vendorPath() . '/theme/' . $path;
    } else {
        $url = '/assets/' . $path;
        $file = SF_PUBLIC_PATH . '/assets/' . $path;
    }

    $mtime = is_file($file) ? filemtime($file) : false;
    return $mtime ? $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $mtime : $url;
}

/**
 * Версия Simflex
 * @return string Версия Simflex
 */
function getSimflexVersion(): string
{
    return Buffer::getOrSet('version', function () {
        if (!InstalledVersions::isInstalled('growtask/simflex')) {
            return 'N/A';
        }

        return InstalledVersions::getPrettyVersion('growtask/simflex');
    });
}

<?php

/*
 * Функции ядра, подключаются автозагрузчиком composer (autoload.files)
 */

use Composer\InstalledVersions;
use Simflex\Core\System\Buffer;

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
    if (!$siteOnly && \Simflex\Core\System\Container::getLocation() === \Simflex\Core\System\Location::Admin) {
        $url = \Simflex\Core\Admin\Page::webVendorPath() . '/theme/' . $path;
        $file = \Simflex\Core\Admin\Page::vendorPath() . '/theme/' . $path;
    } else {
        $url = '/assets/' . $path;
        $file = \Simflex\Core\System\Container::getPaths()->getPublic('assets/' . $path);
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
        if (!InstalledVersions::isInstalled('growtask/simflex-core')) {
            return 'N/A';
        }

        return InstalledVersions::getPrettyVersion('growtask/simflex-core');
    });
}

/**
 * Полный URL
 * @param string $path Путь к ресурсу
 * @param array $get GET-параметры
 * @return string Полный URL
 */
function url(string $path, array $get = []): string
{
    $q = http_build_query($get);
    $request = \Simflex\Core\System\Container::getRequest();
    return ($request?->isHttps() ? 'https' : 'http') . '://' . $request?->getHost() . $path . ($q ? ('?' . $q) : '');
}

/**
 * Выводит SVG-иконку из спрайта сайта public/assets/icons/icons.svg (символ icon-<имя>)
 * @param string $iconName Имя иконки
 * @param string $className Дополнительные CSS-классы
 * @return string HTML SVG со ссылкой на спрайт
 */
function renderIcon(string $iconName, string $className = ''): string
{
    return sprintf(
        '<svg class="%s" aria-hidden="true"><use href="/assets/icons/icons.svg#icon-%s"></use></svg>',
        htmlspecialchars($className),
        htmlspecialchars($iconName)
    );
}

/**
 * Собирает строку HTML-атрибутов из массива ключ => значение
 * @param array<string,mixed> $attributes HTML-атрибуты: ключ => значение
 * @return string Строка HTML-атрибутов с экранированными значениями
 */
function buildAttrs(array $attributes = []): string
{
    if (empty($attributes)) {
        return '';
    }

    return implode(
        ' ',
        array_map(
            fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8')),
            array_keys($attributes),
            $attributes
        )
    );
}

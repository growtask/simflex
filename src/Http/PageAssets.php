<?php

namespace Simflex\Core\Http;

use Simflex\Core\System\Container;
use Simflex\Core\System\Location;
use Simflex\Core\System\Settings;
use Simflex\Core\Admin\Page as AdminPage;

/**
 * Стили и скрипты страницы сайта: подключаются в коде (компоненты, модули, Layout), выводятся темой
 * через Page::getMeta() (стили) и Page::getMetaJs() (скрипты). В админке вызовы передаются Admin\Page.
 *
 * Приоритет $idx: 0 - выводится первым, 100 - последним; повторное подключение файла меняет только приоритет.
 * Настройки сайта static_domain, static_version, static_cache* - выдача статики с отдельного домена и склеенные
 * файлы кэша
 */
class PageAssets
{
    /** @var array<string, int> url => приоритет */
    protected static array $css = [];
    /** @var array<string, array{int, string, bool}> ключ => [приоритет, url или код, встроенный код] */
    protected static array $js = [];

    /**
     * Подключает файл по расширению (.css или .js) или явно указанному типу
     * @param string $file Веб-путь к файлу
     * @param string|null $type css или js
     */
    public static function add(string $file, int $idx = 100, ?string $type = null): void
    {
        $type ??= str_contains($file, '.css') ? 'css' : (str_contains($file, '.js') ? 'js' : null);
        match ($type) {
            'css' => static::css($file, $idx),
            'js' => static::js($file, $idx),
            default => throw new \InvalidArgumentException("Unknown asset type (css or js?) for $file"),
        };
    }

    public static function css(string $file, int $idx = 100): void
    {
        if (Container::getLocation() === Location::Admin) {
            AdminPage::get()->css($file, $idx);
            return;
        }
        static::$css[$file] = $idx;
    }

    public static function js(string $file, int $idx = 100): void
    {
        if (Container::getLocation() === Location::Admin) {
            AdminPage::get()->js($file, $idx);
            return;
        }
        static::$js[$file] = [$idx, $file, false];
    }

    /**
     * Встроенный скрипт в блоке скриптов страницы. В админке не выводится
     */
    public static function jsInline(string $js, int $idx = 100): void
    {
        if (Container::getLocation() === Location::Admin) {
            return;
        }
        static::$js['inline:' . md5($js)] = [$idx, $js, true];
    }

    /**
     * HTML подключения стилей
     */
    public static function getCssHtml(): string
    {
        $v = (int)Settings::get('static_version');
        [$files, $cached] = static::withCache(static::sorted(static::$css), 'css', (string)$v);

        $html = '';
        foreach ($files as $css) {
            if (isset($cached[$css])) {
                continue;
            }
            $href = static::onStaticDomain($css, 'css') . ($v ? '?v=' . $v : '');
            $html .= '<link type="text/css" rel="preload" as="style" href="' . $href . '" />' . "\r\n";
            $html .= '<link type="text/css" rel="stylesheet" href="' . $href . '" />' . "\r\n";
        }

        return $html;
    }

    /**
     * HTML подключения скриптов
     */
    public static function getJsHtml(): string
    {
        $v = (string)env('JS_VER');
        $items = static::$js;
        uasort($items, fn(array $a, array $b) => $a[0] <=> $b[0]);
        $files = array_map(fn(array $item) => $item[1], array_filter($items, fn(array $item) => !$item[2]));
        [, $cached] = static::withCache(array_values($files), 'js', $v);
        if ($cached) {
            $items = ['cache' => [1, "/cache/js/$v.min.js", false]] + $items;
        }

        $html = '';
        foreach ($items as [, $js, $inline]) {
            if ($inline) {
                $html .= '<script type="text/javascript" defer>' . $js . '</script>' . "\r\n";
                continue;
            }
            if (isset($cached[$js])) {
                continue;
            }
            $src = static::onStaticDomain($js, 'js') . ($v && !str_contains($js, 'http') ? '?v=' . $v : '');
            $html .= '<script type="text/javascript" defer src="' . $src . '"></script>' . "\r\n";
        }

        return $html;
    }

    /**
     * @param array<string, int> $files url => приоритет
     * @return list<string> url по приоритету
     */
    protected static function sorted(array $files): array
    {
        asort($files);
        return array_keys($files);
    }

    /**
     * Склеенный файл кэша (настройки static_cache, static_cache_css / static_cache_js - список вошедших в него
     * файлов): файлы из списка заменяются файлом кэша
     * @param list<string> $files
     * @return array{list<string>, array<string, int>} файлы для вывода и вошедшие в кэш
     */
    protected static function withCache(array $files, string $type, string $version): array
    {
        $list = Settings::get('static_cache') ? (string)Settings::get('static_cache_' . $type) : '';
        $cached = array_flip(array_filter(array_map('trim', explode("\n", $list))));
        if ($cached && $type === 'css') {
            array_unshift($files, "/cache/css/$version.min.css");
        }

        return [$files, $cached];
    }

    /**
     * Адрес на домене статики (настройка static_domain, static_domain_sub - поддомен css. / js.)
     */
    protected static function onStaticDomain(string $url, string $type): string
    {
        $domain = (string)Settings::get('static_domain');
        if (!$domain || str_contains($url, 'http')) {
            return $url;
        }

        return 'http://' . (Settings::get('static_domain_sub') ? "$type." : '') . $domain . $url;
    }
}

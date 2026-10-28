<?php

namespace Simflex\Core;

/**
 * Заголовок и мета-теги страницы сайта. Задаются компонентом страницы или разделом «SEO» админки (таблица seo,
 * главнее компонента), выводятся темой через Page::meta()
 */
class PageSeo
{
    protected static string $title = '';
    protected static string $description = '';
    protected static string $keywords = '';
    protected static string $metatags = '';
    protected static string $metaRaw = '';
    protected static bool $isFinal = false;
    protected static bool $isExactTitle = false;

    /**
     * Задаёт заголовок и мета-теги, если их ещё не зафиксировали
     * @param string $title Заголовок страницы
     * @param string|null $description Описание, пустое - не меняется
     * @param string|null $keywords Ключевые слова, пустые - не меняются
     * @param bool $final Зафиксировать: следующие вызовы ничего не меняют
     * @param string|null $metatags Произвольные теги в head
     * @param bool $exactTitle Заголовок окна - $title без названия сайта
     */
    public static function seo(
        string $title,
        ?string $description = '',
        ?string $keywords = '',
        bool $final = false,
        ?string $metatags = '',
        bool $exactTitle = false,
    ): void {
        if (static::$isFinal) {
            return;
        }
        static::$title = $title;
        static::$description = $description ?: static::$description;
        static::$keywords = $keywords ?: static::$keywords;
        static::$metatags = $metatags ?: static::$metatags;
        static::$isFinal = $final;
        static::$isExactTitle = $exactTitle;
    }

    /**
     * Добавляет произвольную строку в head
     */
    public static function addMetaRaw(string $str): void
    {
        static::$metaRaw .= "$str\n";
    }

    public static function getTitle(): string
    {
        return static::$title;
    }

    /**
     * Заголовок окна: «Заголовок | Название сайта» (настройка site_name)
     */
    public static function getFullTitle(): string
    {
        $title = str_replace('<br/>', '', static::$title);
        if (static::$isExactTitle) {
            return $title;
        }

        return $title . ($title !== '' ? ' | ' : '') . SiteSettings::get('site_name');
    }

    public static function getDescription(): string
    {
        return static::$description;
    }

    public static function getKeywords(): string
    {
        return static::$keywords;
    }

    /**
     * title, description, keywords и произвольные теги
     */
    public static function getHeadHtml(): string
    {
        $html = '<title>' . htmlspecialchars(static::getFullTitle()) . '</title>' . "\r\n";
        if (static::$description !== '') {
            $html .= '<meta name="description" content="' . htmlspecialchars(static::$description) . '"/>' . "\r\n";
        }
        if (static::$keywords !== '') {
            $html .= '<meta name="keywords" content="' . htmlspecialchars(static::$keywords) . '"/>' . "\r\n";
        }
        $html .= static::$metatags ? static::$metatags . "\r\n" : '';

        return $html . static::$metaRaw;
    }
}

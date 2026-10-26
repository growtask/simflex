<?php

namespace Simflex\Extensions\Content;

/**
 * Шаблоны страниц, объявленные в коде: provider/templates.php приложения возвращает ключ шаблона => ContentTemplate.
 * Страница (content.template) ссылается на свой шаблон по ключу
 */
class ContentTemplates
{
    /** Файл шаблона для страницы без шаблона или с неизвестным шаблоном */
    public const DEFAULT_FILE = 'default.tpl';

    /** @var array<string, ContentTemplate>|null */
    protected static ?array $templates = null;

    /**
     * @return array<string, ContentTemplate> ключ => шаблон
     */
    public static function all(): array
    {
        if (static::$templates === null) {
            $file = SF_ROOT_PATH . '/provider/templates.php';
            $data = is_file($file) ? require $file : [];

            static::$templates = [];
            foreach (is_array($data) ? $data : [] as $key => $template) {
                if (is_string($key) && $template instanceof ContentTemplate) {
                    static::$templates[$key] = $template;
                }
            }
        }

        return static::$templates;
    }

    public static function get(?string $key): ?ContentTemplate
    {
        return $key === null ? null : static::all()[$key] ?? null;
    }

    public static function file(?string $key): string
    {
        return static::get($key)?->file ?? static::DEFAULT_FILE;
    }

    public static function reset(): void
    {
        static::$templates = null;
    }
}

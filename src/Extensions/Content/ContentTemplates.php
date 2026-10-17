<?php

namespace Simflex\Extensions\Content;

/**
 * Page templates declared in code: the app's provider/templates.php returns template key => ContentTemplate.
 * A page (content.template) refers to its template by the key
 */
class ContentTemplates
{
    /** Template file of a page without a template or with an unknown one */
    public const DEFAULT_FILE = 'default.tpl';

    /** @var array<string, ContentTemplate>|null */
    protected static ?array $templates = null;

    /**
     * @return array<string, ContentTemplate> key => template
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

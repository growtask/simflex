<?php

namespace Simflex\Admin\Plugins\Editor;

use Simflex\Admin\Core;
use Simflex\Core\Container;

/**
 * Класс Editor
 *
 * Доступ к JavaScript WYSIWYG-редакторам
 *
 */
class Editor
{
    public static array $inited = [];

    /**
     * Регистрирует скрипт TinyMCE и возвращает HTML инициализации редактора (один раз на тип редактора)
     */
    public static function tinymce(string $type, string $css_class = ''): string
    {
        $tinyMcePath = Core::webVendorPath() . '/Plugins/Editor/tinymce/tinymce.min.js';
        Container::getPage()::js($tinyMcePath . '" referrerpolicy="origin', 10);

        if (!empty(self::$inited[$type])) {
            return '';
        }
        self::$inited[$type] = true;

        return <<<HTML
<script>
tinymce.init({
    selector: '.$css_class',
    language: 'ru',
    plugins: 'fm lists table link emoticons code media',
    menu: {
        file: {
            items: ''
        },
        view: {
            items: ''
        },
        insert: {
            title: 'Insert',
            items: 'hr fm media'
        },
        format: {
            items: ''
        }
    },
    toolbar: 'undo redo | blocks | bold italic strikethrough | numlist bullist table link emoticons fm | code'
});
</script>
<style>
.tox-promotion {
    display: none !important;
}
</style>
HTML;
    }
}

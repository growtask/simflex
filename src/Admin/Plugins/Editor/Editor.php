<?php

namespace Simflex\Admin\Plugins\Editor;

use Simflex\Admin\Core;
use Simflex\Core\Container;

/**
 * Editor class
 *
 * Get access to javascript WYSIWYG Editors
 *
 */
class Editor
{
    public static array $inited = [];

    /**
     * Registers the TinyMCE script and returns the editor init HTML (once per editor type)
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
